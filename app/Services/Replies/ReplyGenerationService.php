<?php

namespace App\Services\Replies;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use App\Services\AI\AIService;
use App\Services\AI\DTOs\ReplyGenerationRequest;
use App\Services\Prompts\ReplyPromptBuilder;
use App\Services\Replies\DTOs\GeneratedReplyData;
use App\Services\Replies\Exceptions\InvalidAiResponseException;
use App\Services\Usage\UsageLimitService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReplyGenerationService
{
    public function __construct(
        protected AIService $aiService,
        protected ReplyPromptBuilder $promptBuilder,
        protected UsageLimitService $usageLimitService,
    ) {}

    /**
     * @param  array<string, mixed>  $input
     */
    public function generate(User $user, array $input, ?int $chatId = null): GeneratedReplyData
    {
        $this->usageLimitService->ensureCanGenerate($user);

        $payload = [
            'message' => (string) ($input['message'] ?? ''),
            'tone' => $input['tone'] ?? 'Professional',
            'use_case' => $input['use_case'] ?? 'General Reply',
            'language' => $input['language'] ?? 'English Improvement',
            'context' => $input['context'] ?? null,
            'goal' => $input['goal'] ?? null,
            'receiver' => $input['receiver'] ?? null,
            'platform' => $input['platform'] ?? null,
            'variants' => $input['variants'] ?? [],
        ];

        $builtPrompt = $this->promptBuilder->build($payload);

        $aiResult = $this->aiService->generateReply(new ReplyGenerationRequest(
            prompt: $builtPrompt->userPrompt,
            systemPrompt: $builtPrompt->systemPrompt,
            provider: null,
            temperature: 0.4,
            maxOutputTokens: 800,
            metadata: [
                'plan' => $user->plan,
                'module' => 'reply_generator',
            ],
        ));

        $parsed = $this->parseAiResponse($aiResult->content);
        $primaryProvider = strtolower((string) config('ai.default_provider', 'gemini'));

        return DB::transaction(function () use ($user, $input, $chatId, $parsed, $aiResult, $primaryProvider): GeneratedReplyData {
            $chat = $this->resolveChat($user, $input, $chatId);

            $userMessage = $chat->messages()->create([
                'user_id' => $user->id,
                'role' => 'user',
                'message_type' => 'raw_input',
                'input_text' => (string) $input['message'],
                'meta' => [
                    'mode' => $input['mode'] ?? 'quick',
                    'tone' => $input['tone'] ?? 'Professional',
                    'use_case' => $input['use_case'] ?? 'General Reply',
                    'language' => $input['language'] ?? 'English Improvement',
                    'context' => $input['context'] ?? null,
                    'goal' => $input['goal'] ?? null,
                    'receiver' => $input['receiver'] ?? null,
                    'platform' => $input['platform'] ?? null,
                ],
            ]);

            $assistantMessage = $chat->messages()->create([
                'user_id' => $user->id,
                'role' => 'assistant',
                'message_type' => 'generated_reply',
                'output_text' => $parsed['best_reply'],
                'meta' => [
                    'risk_note' => $parsed['risk_note'],
                    'coach_note' => $parsed['coach_note'],
                    'next_step' => $parsed['next_step'],
                    'variants' => $parsed['variants'],
                    'provider' => $aiResult->provider,
                    'model' => $aiResult->model,
                    'finish_reason' => $aiResult->finishReason,
                    'usage' => $aiResult->usage,
                ],
            ]);

            $chat->forceFill([
                'last_message_at' => now(),
            ])->save();

            $this->usageLimitService->incrementRepliesGenerated($user);

            return new GeneratedReplyData(
                chat: $chat->fresh(),
                userMessage: $userMessage,
                assistantMessage: $assistantMessage,
                bestReply: $parsed['best_reply'],
                riskNote: $parsed['risk_note'],
                coachNote: $parsed['coach_note'],
                nextStep: $parsed['next_step'],
                variants: $parsed['variants'],
                provider: $aiResult->provider,
                model: $aiResult->model,
                finishReason: $aiResult->finishReason,
                usage: $aiResult->usage,
                usedFallback: strtolower($aiResult->provider) !== $primaryProvider,
            );
        });
    }

    /**
     * @param  array<string, mixed>  $input
     */
    protected function resolveChat(User $user, array $input, ?int $chatId): Chat
    {
        if ($chatId !== null) {
            $chat = Chat::query()
                ->where('id', $chatId)
                ->where('user_id', $user->id)
                ->first();

            if ($chat !== null) {
                return $chat;
            }
        }

        return $user->chats()->create([
            'title' => Str::limit(trim((string) $input['message']), 70, '...'),
            'mode' => $input['mode'] ?? 'quick',
            'last_message_at' => now(),
        ]);
    }

    /**
     * @return array{best_reply:string, risk_note:?string, coach_note:?string, next_step:?string, variants:array<string, string>}
     */
    protected function parseAiResponse(string $content): array
    {
        $decoded = json_decode($this->extractJsonPayload($content), true);

        if (! is_array($decoded)) {
            throw new InvalidAiResponseException(message: 'AI provider returned invalid JSON.');
        }

        $bestReply = trim((string) ($decoded['best_reply'] ?? ''));

        if ($bestReply === '') {
            throw new InvalidAiResponseException(message: 'AI provider returned JSON without a best_reply.');
        }

        $riskNote = $decoded['risk_note'] ?? null;
        $riskNote = $riskNote !== null ? trim((string) $riskNote) : null;
        $riskNote = ($riskNote !== '' && $riskNote !== 'null') ? $riskNote : null;

        $coachNote = $decoded['coach_note'] ?? null;
        $coachNote = $coachNote !== null ? trim((string) $coachNote) : null;
        $coachNote = ($coachNote !== '' && $coachNote !== 'null') ? $coachNote : null;

        $nextStep = $decoded['next_step'] ?? null;
        $nextStep = $nextStep !== null ? trim((string) $nextStep) : null;
        $nextStep = ($nextStep !== '' && $nextStep !== 'null') ? $nextStep : null;

        $variants = $decoded['variants'] ?? [];

        if ($variants instanceof \stdClass) {
            $variants = [];
        }

        if (! is_array($variants)) {
            $variants = [];
        }

        $normalizedVariants = [];

        foreach ($variants as $key => $value) {
            $text = trim((string) $value);

            if ($text !== '') {
                $normalizedVariants[(string) $key] = $text;
            }
        }

        return [
            'best_reply' => $bestReply,
            'risk_note' => $riskNote,
            'coach_note' => $coachNote,
            'next_step' => $nextStep,
            'variants' => $normalizedVariants,
        ];
    }

    protected function extractJsonPayload(string $content): string
    {
        $trimmed = trim($content);

        if (str_starts_with($trimmed, '```')) {
            $trimmed = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $trimmed) ?? $trimmed;
            $trimmed = trim($trimmed);
        }

        // If content has preamble text before the JSON object, extract just the object
        if (! str_starts_with($trimmed, '{')) {
            $start = strpos($trimmed, '{');
            $end = strrpos($trimmed, '}');

            if ($start !== false && $end !== false && $end > $start) {
                $trimmed = substr($trimmed, $start, $end - $start + 1);
            }
        }

        return trim($trimmed);
    }
}
