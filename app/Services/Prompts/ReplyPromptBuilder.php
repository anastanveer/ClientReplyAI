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
            'You are ClientReplyAI, a focused AI communication assistant.',
            'Your only job is to turn rough user messages into polished, human, ready-to-send replies.',
            'Preserve the user meaning.',
            'Avoid robotic wording and generic filler.',
            'Do not invent facts, promises, timelines, approvals, or commitments the user did not provide.',
            'Do not overpromise.',
            'Make the reply practical, clear, and natural.',
            $context->toneRule['instruction'],
            $context->useCaseRule['instruction'],
            $context->languageRule['instruction'],
            'Default to the lowest-cost useful output: one best recommended reply only.',
        ];

        if ($context->shouldIncludeRiskNote()) {
            $lines[] = 'If the original wording may sound rude, risky, weak, hostile, or overpromising, include one short risk_note with safer wording guidance.';
        } else {
            $lines[] = 'Only include a risk_note if there is a genuine wording risk.';
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
        $sections[] = '- Keep the reply ready to send.';
        $sections[] = '- Improve grammar and clarity.';
        $sections[] = '- Keep the user intent intact.';
        $sections[] = '- Do not add false details.';
        $sections[] = '- Return JSON only.';

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
