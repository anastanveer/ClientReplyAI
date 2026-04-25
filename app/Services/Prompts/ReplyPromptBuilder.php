<?php

namespace App\Services\Prompts;

use App\Services\AIEngine\AIEngineContext;
use App\Services\AIEngine\ReplyStrategyBuilder;
use App\Services\Prompts\DTOs\BuiltPrompt;
use App\Services\Prompts\DTOs\ResolvedPromptContext;

class ReplyPromptBuilder
{
    public function __construct(
        protected PromptContextResolver $contextResolver,
        protected ReplyStrategyBuilder $strategyBuilder,
    ) {}

    /**
     * @param  array<string, mixed>  $input
     */
    public function build(array $input): BuiltPrompt
    {
        $context = $this->contextResolver->resolve($input);

        // Run the AI Engine — pure PHP, no extra API calls
        $engineContext = $this->strategyBuilder->build(
            message:  $context->message,
            useCase:  $context->useCaseRule['label'],
            tone:     $context->toneRule['label'],
            receiver: $context->receiver,
            context:  $context->additionalContext,
            platform: $context->platform,
        );

        return new BuiltPrompt(
            systemPrompt: $this->buildSystemPrompt($context),
            userPrompt: $this->buildUserPrompt($context, $engineContext),
            variants: $context->allowedVariants,
            riskFlags: $context->riskFlags,
            responseSchema: $this->responseSchema($context),
        );
    }

    protected function buildSystemPrompt(ResolvedPromptContext $context): string
    {
        $lines = [
            'You are ClientReplyAI — a professional communication decision engine used by freelancers, business owners, and professionals worldwide.',
            'You are not a generic AI text generator. You are a senior communication strategist who writes replies that achieve real outcomes.',
            '',
            '## Role',
            'Transform rough or weak messages into highly accurate, human-like, outcome-driven replies that are ready to send without any editing.',
            'Write entirely in the first-person voice of the sender. Never describe, explain, or narrate — just write the reply itself.',
            '',
            '## Intelligence layer — apply every time',
            '1. Detect the sender\'s real intent automatically (payment follow-up, negotiation, apology, complaint, delay, recruiter, requirement, etc.).',
            '2. Infer the likely receiver attitude from the context (strict client, friendly contact, corporate manager, ghosting contact, price-sensitive buyer).',
            '3. Choose wording that achieves the specific outcome — not just wording that sounds polished.',
            '4. If the input message is weak, vague, or poorly written: elevate it intelligently using the use-case context. Do not ask questions.',
            '',
            '## Quality standards',
            '- Sound like an experienced professional wrote this — confident, natural, specific, purposeful.',
            '- Add subtle human warmth where it fits. Cold and robotic is never acceptable.',
            '- NEVER use AI openers: "I hope this message finds you well", "I wanted to reach out", "As per our previous conversation", "Certainly!", "Of course!", "Great question!", "Happy to help!", "I understand your concern."',
            '- NEVER start with the sender\'s name or a greeting unless it matches the platform norm (e.g. WhatsApp).',
            '- Write at least 2–3 sentences unless the tone is Short or Direct.',
            '- Every reply must be copy-paste ready with zero editing required.',
            '- Never overpromise, never invent facts, timelines, approvals, or commitments not present in the input.',
            '',
            $context->toneRule['instruction'],
            $context->useCaseRule['instruction'],
            $context->languageRule['instruction'],
        ];

        if ($context->shouldIncludeRiskNote()) {
            $lines[] = 'Include a concise risk_note: identify the exact wording risk and suggest a safer phrasing.';
        } else {
            $lines[] = 'Set risk_note only for genuine risks: rude tone, overpromise, weak positioning, or a critically missing detail that changes reply quality. Otherwise set risk_note to null.';
        }

        $lines[] = 'Set coach_note to one short sentence explaining WHY this reply achieves the goal (e.g. "Opens with acknowledgment to lower defensiveness, then states the clear ask."). If there is nothing non-obvious, set coach_note to null.';
        $lines[] = 'Set next_step to a short, specific, actionable recommendation for the sender\'s next move AFTER sending this reply (e.g. "Follow up in 48 hours if no response."). Set to null if no follow-up is needed.';

        if ($context->hasOptionalVariants()) {
            $lines[] = 'Generate optional variants only for the specifically requested variant keys — nothing extra.';
        }

        $lines[] = $this->jsonInstruction($context);

        return implode("\n", $lines);
    }

    protected function buildUserPrompt(ResolvedPromptContext $context, ?AIEngineContext $engineContext = null): string
    {
        $sections = [
            '## TASK',
            'Rewrite the message below into a polished, human, outcome-driven reply.',
            '',
            '## INPUT MESSAGE',
            $context->message,
            '',
            '## PARAMETERS',
            'Tone: '.$context->toneRule['label'],
            'Use case: '.$context->useCaseRule['label'],
            'Language: '.$context->languageRule['label'],
        ];

        if ($context->additionalContext !== null) {
            $sections[] = 'Background context: '.$context->additionalContext;
        }

        if ($context->goal !== null) {
            $sections[] = 'Sender goal: '.$context->goal;
        }

        if ($context->receiver !== null) {
            $sections[] = 'Receiver type: '.$context->receiver;
        }

        if ($context->platform !== null) {
            $sections[] = 'Platform: '.$context->platform;
        }

        // Inject AI Engine analysis — this is the intelligence layer
        if ($engineContext !== null) {
            $sections[] = '';
            $sections[] = $engineContext->toPromptSection();
        }

        if ($context->hasOptionalVariants()) {
            $sections[] = '';
            $sections[] = '## VARIANTS REQUESTED';
            $sections[] = 'Also generate: '.$this->formatVariants($context);
        }

        $sections[] = '';
        $sections[] = '## OUTPUT RULES';
        $sections[] = '- Reply must be copy-paste ready with zero editing.';
        $sections[] = '- Sound human and purposeful — not templated, not robotic.';
        $sections[] = '- Preserve the sender\'s intent and all factual claims exactly.';
        $sections[] = '- Do not add invented details, timelines, promises, or commitments.';
        $sections[] = '- Return JSON only — no markdown, no explanation outside the JSON object.';

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
            'coach_note' => 'string|null',
            'next_step' => 'string|null',
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

        return 'Return valid minified JSON with exactly these keys: {"best_reply":"string","risk_note":"string|null","coach_note":"string|null","next_step":"string|null",'.$variantInstruction.'}.';
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
