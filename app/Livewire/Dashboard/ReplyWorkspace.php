<?php

namespace App\Livewire\Dashboard;

use App\Models\SavedReply;
use App\Models\Template;
use App\Services\AI\Exceptions\AIException;
use App\Services\Replies\ReplyGenerationService;
use App\Services\Replies\Exceptions\InvalidAiResponseException;
use App\Services\Usage\Exceptions\DailyLimitExceededException;
use App\Services\Usage\UsageLimitService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class ReplyWorkspace extends Component
{
    public string $mode = 'quick';

    public bool $advancedOpen = false;

    public string $composer = 'Hi, I finished the work already but payment is still pending. I want to follow up politely and ask them to clear it today.';

    public string $tone = 'Professional';

    public string $useCase = 'Payment Reminder';

    public string $language = 'English Improvement';

    public string $context = '';

    public string $goal = '';

    public string $receiver = '';

    public string $platform = '';

    public ?int $currentChatId = null;

    public ?int $lastAssistantMessageId = null;

    public ?int $savedReplyId = null;

    public ?string $lastSubmittedMessage = null;

    public ?string $bestReply = null;

    public ?string $riskNote = null;

    /**
     * @var array<string, string>
     */
    public array $variants = [];

    public ?string $providerStatus = null;

    public ?string $errorMessage = null;

    public int $qualityScore = 0;

    public int $dailyUsage = 0;

    public ?int $dailyLimit = 5;

    public ?int $dailyRemaining = 5;

    public function mount(): void
    {
        // Profile defaults: lowest priority — applied first
        $profile = auth()->user()->profile;
        if ($profile) {
            $this->tone = $profile->preferred_tone ?: $this->tone;
            $this->useCase = $profile->default_use_case ?: $this->useCase;
            $this->language = $profile->default_language ?: $this->language;
        }

        // Template session: overrides profile defaults
        if (session()->has('apply_template')) {
            $data = session()->pull('apply_template');
            $this->applySuggestion(
                (string) ($data['content'] ?? $this->composer),
                $data['use_case'] ?? null,
                $data['tone'] ?? null,
                $data['language'] ?? null,
            );
        }

        // Reuse: highest priority for composer text
        if (session()->has('reuse_reply_text')) {
            $this->composer = session()->pull('reuse_reply_text');
        }

        $this->refreshUsageSummary();
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'composer' => ['required', 'string', 'min:5', 'max:5000'],
            'tone' => ['required', 'string', 'in:'.implode(',', $this->toneOptions())],
            'useCase' => ['required', 'string', 'in:'.implode(',', $this->useCaseOptions())],
            'language' => ['required', 'string', 'in:'.implode(',', $this->languageOptions())],
            'context' => ['nullable', 'string', 'max:2000'],
            'goal' => ['nullable', 'string', 'max:1000'],
            'receiver' => ['nullable', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'max:255'],
            'mode' => ['required', 'string', 'in:quick,advanced'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'composer.required' => 'Paste the rough message you want help replying to.',
            'composer.min' => 'The message is too short to rewrite properly.',
            'tone.in' => 'Choose a valid tone option.',
            'useCase.in' => 'Choose a valid use-case option.',
            'language.in' => 'Choose a valid language option.',
        ];
    }

    public function applySuggestion(string $message, ?string $useCase = null, ?string $tone = null, ?string $language = null): void
    {
        $this->composer = $message;
        $this->useCase = $useCase ?: $this->useCase;
        $this->tone = $tone ?: $this->tone;
        $this->language = $language ?: $this->language;

        $this->resetGenerationFeedback();
    }

    public function applyTemplate(int $id): void
    {
        $template = Template::find($id);

        if ($template === null) {
            return;
        }

        $this->applySuggestion(
            $template->content,
            $template->use_case,
            $template->tone,
            $template->language,
        );
    }

    public function generateReply(): void
    {
        $validated = $this->validate();

        $this->resetErrorBag();
        $this->errorMessage = null;

        try {
            $result = app(ReplyGenerationService::class)->generate(
                auth()->user(),
                [
                    'message' => $validated['composer'],
                    'tone' => $validated['tone'],
                    'use_case' => $validated['useCase'],
                    'language' => $validated['language'],
                    'context' => $this->advancedOpen ? $validated['context'] : null,
                    'goal' => $this->advancedOpen ? $validated['goal'] : null,
                    'receiver' => $this->advancedOpen ? $validated['receiver'] : null,
                    'platform' => $this->advancedOpen ? $validated['platform'] : null,
                    'mode' => $validated['mode'],
                    'variants' => [],
                ],
                $this->currentChatId,
            );

            $this->currentChatId = $result->chat->id;
            $this->lastAssistantMessageId = $result->assistantMessage->id;
            $this->savedReplyId = null;
            $this->lastSubmittedMessage = $validated['composer'];
            $this->bestReply = $result->bestReply;
            $this->riskNote = $result->riskNote;
            $this->variants = $result->variants;
            $this->qualityScore = $this->estimateQualityScore($result->bestReply, $result->riskNote);
            $this->providerStatus = $result->usedFallback
                ? sprintf('Gemini was unavailable, so %s generated this reply as fallback.', ucfirst($result->provider))
                : sprintf('Generated with %s.', ucfirst($result->provider));

            $this->refreshUsageSummary();
        } catch (DailyLimitExceededException|InvalidAiResponseException $exception) {
            $this->errorMessage = $exception->userMessage();
            $this->refreshUsageSummary();
        } catch (AIException $exception) {
            $this->errorMessage = $exception->userMessage();
            $this->refreshUsageSummary();
        }
    }

    public function saveReply(): void
    {
        if ($this->bestReply === null || $this->currentChatId === null || $this->savedReplyId !== null) {
            return;
        }

        $saved = auth()->user()->savedReplies()->create([
            'chat_id' => $this->currentChatId,
            'source_message_id' => $this->lastAssistantMessageId,
            'title' => Str::limit($this->lastSubmittedMessage ?? $this->composer, 70),
            'reply_text' => $this->bestReply,
            'is_favorite' => false,
            'meta' => [
                'tone' => $this->tone,
                'use_case' => $this->useCase,
                'language' => $this->language,
            ],
        ]);

        $this->savedReplyId = $saved->id;
    }

    public function render(): View
    {
        return view('livewire.dashboard.reply-workspace', [
            'toneOptions' => $this->toneOptions(),
            'useCaseOptions' => $this->useCaseOptions(),
            'languageOptions' => $this->languageOptions(),
            'hasReply' => $this->bestReply !== null,
            'quickTemplates' => Template::orderBy('name')->take(6)->get(),
        ]);
    }

    /**
     * @return list<string>
     */
    protected function toneOptions(): array
    {
        return ['Professional', 'Friendly', 'Polite', 'Confident', 'Strong', 'Soft', 'Direct', 'Respectful', 'Short', 'Detailed'];
    }

    /**
     * @return list<string>
     */
    protected function useCaseOptions(): array
    {
        return [
            'Fiverr Client Reply',
            'WhatsApp Business Reply',
            'Email Reply',
            'Job Recruiter Reply',
            'LinkedIn Reply',
            'Proposal Reply',
            'Complaint Reply',
            'Apology Reply',
            'Follow-up Reply',
            'Negotiation Reply',
            'Project Update Reply',
            'Payment Reminder',
            'Delay Update',
            'Asking for Requirements',
            'Asking for Access',
            'Review Request',
            'Support Response',
            'Friendly Daily Reply',
            'Translation Improvement',
            'General Reply',
        ];
    }

    /**
     * @return list<string>
     */
    protected function languageOptions(): array
    {
        return [
            'English Improvement',
            'Broken English to Professional English',
            'Roman Urdu to Professional English',
            'Urdu to English',
            'Hindi to English',
            'English to German',
            'English to French',
            'English to Arabic',
        ];
    }

    protected function refreshUsageSummary(): void
    {
        $summary = app(UsageLimitService::class)->usageSummary(auth()->user());

        $this->dailyUsage = $summary['used'];
        $this->dailyLimit = $summary['limit'];
        $this->dailyRemaining = $summary['remaining'];
    }

    protected function resetGenerationFeedback(): void
    {
        $this->errorMessage = null;
        $this->bestReply = null;
        $this->riskNote = null;
        $this->variants = [];
        $this->providerStatus = null;
        $this->qualityScore = 0;
        $this->savedReplyId = null;
        $this->lastAssistantMessageId = null;
    }

    protected function estimateQualityScore(string $reply, ?string $riskNote): int
    {
        $score = 82;
        $wordCount = str_word_count($reply);

        if ($wordCount >= 18 && $wordCount <= 70) {
            $score += 8;
        }

        if (! str_contains($reply, '  ')) {
            $score += 3;
        }

        if ($riskNote === null) {
            $score += 4;
        }

        return min($score, 98);
    }
}
