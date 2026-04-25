<?php

namespace App\Services\Prompts;

use App\Services\Prompts\DTOs\BuiltPrompt;
use App\Services\Prompts\DTOs\ResolvedPromptContext;

class ReplyPromptBuilder
{
    public function __construct(
        protected PromptContextResolver $contextResolver,
    ) {}

    /**
     * @param  array<string, mixed>  $input
     */
    public function build(array $input): BuiltPrompt
    {
        $context = $this->contextResolver->resolve($input);

        return new BuiltPrompt(
            systemPrompt: $this->buildSystemPrompt($context),
            userPrompt: $this->buildUserPrompt($context),
            variants: $context->allowedVariants,
            riskFlags: $context->riskFlags,
            responseSchema: $this->responseSchema($context),
        );
    }

    protected function buildSystemPrompt(ResolvedPromptContext $context): string
    {
        $lines = [
            'You are ClientReplyAI, a professional communication assistant.',
            'Your only job is to turn rough user messages into polished, human, ready-to-send replies.',
            'Write in the first-person voice of the sender — not as an AI explaining or describing the reply.',
            'Preserve the sender\'s meaning exactly. Do not invent facts, promises, timelines, approvals, or commitments not in the original.',
            'Never use AI filler: avoid "I hope this message finds you well", "I wanted to reach out", "As per our previous conversation", "Certainly!", "Of course!", or similar openers.',
            'Sound like a real professional wrote it — confident, natural, specific, and ready to send.',
            'Write at least 2–3 complete sentences unless the selected tone is Short or Direct.',
            'If the input is brief or vague, still produce the best possible reply for the use case. Use risk_note only if a detail was missing that meaningfully affects the reply.',
            $context->toneRule['instruction'],
            $context->useCaseRule['instruction'],
            $context->languageRule['instruction'],
        ];

        if ($context->shouldIncludeRiskNote()) {
            $lines[] = 'Include a short risk_note identifying the specific wording risk and what phrasing would be safer.';
        } else {
            $lines[] = 'Only set risk_note if there is a genuine wording risk or a key missing detail that affects reply quality. Otherwise set risk_note to null.';
        }

        if ($context->hasOptionalVariants()) {
            $lines[] = 'Generate optional variants only for the specifically requested variant keys and nothing extra.';
        }

        $lines[] = $this->jsonInstruction($context);

        return implode("\n", $lines);
    }

    protected function buildUserPrompt(ResolvedPromptContext $context): string
    {
        $sections = [
            'TASK',
            'Rewrite the message into a polished, human, ready-to-send reply.',
            '',
            'INPUT',
            'Original message:',
            $context->message,
            '',
            'CONTEXT SUMMARY',
            'Tone: '.$context->toneRule['label'],
            'Use case: '.$context->useCaseRule['label'],
            'Language handling: '.$context->languageRule['label'],
        ];

        if ($context->additionalContext !== null) {
            $sections[] = 'Additional context: '.$context->additionalContext;
        }

        if ($context->goal !== null) {
            $sections[] = 'User goal: '.$context->goal;
        }

        if ($context->receiver !== null) {
            $sections[] = 'Receiver: '.$context->receiver;
        }

        if ($context->platform !== null) {
            $sections[] = 'Platform: '.$context->platform;
        }

        if ($context->hasOptionalVariants()) {
            $sections[] = 'Requested optional variants: '.$this->formatVariants($context);
        } else {
            $sections[] = 'Requested optional variants: none. Generate only the best recommended reply.';
        }

        if ($context->riskFlags !== []) {
            $sections[] = 'Detected wording risks: '.implode(', ', $context->riskFlags).'.';
        }

        $sections[] = '';
        $sections[] = 'OUTPUT RULES';
        $sections[] = '- Reply must be ready to copy and send without editing.';
        $sections[] = '- Sound human and professional, not templated or robotic.';
        $sections[] = '- Preserve the sender\'s intent and keep all factual claims.';
        $sections[] = '- Do not add invented details, timelines, or promises.';
        $sections[] = '- If input is vague, write the best-fit reply and note missing context in risk_note only if it materially affects quality.';
        $sections[] = '- Return JSON only — no markdown, no extra explanation.';

        return implode("\n", $sections);
    }

    /**
     * @return array<string, mixed>
     */
    protected function responseSchema(ResolvedPromptContext $context): array
    {
        $schema = [
            'best_reply' => 'string',
            'risk_note' => 'string|null',
            'variants' => 'object',
        ];

        if ($context->allowedVariants === []) {
            $schema['variants'] = new \stdClass();

            return $schema;
        }

        $variantSchema = [];

        foreach ($context->allowedVariants as $variant) {
            $variantSchema[$variant] = 'string';
        }

        $schema['variants'] = $variantSchema;

        return $schema;
    }

    protected function jsonInstruction(ResolvedPromptContext $context): string
    {
        $variantInstruction = $context->allowedVariants === []
            ? '"variants": {}'
            : '"variants": {'.implode(', ', array_map(
                static fn (string $variant): string => sprintf('"%s": "string"', $variant),
                $context->allowedVariants,
            )).'}';

        return 'Return valid minified JSON with exactly these keys: {"best_reply":"string","risk_note":"string|null",'.$variantInstruction.'}.';
    }

    protected function formatVariants(ResolvedPromptContext $context): string
    {
        $rules = $this->contextResolver->variantRules();

        return implode(', ', array_map(
            static fn (string $variant): string => $rules[$variant]['label'] ?? $variant,
            $context->allowedVariants,
        ));
    }
}
