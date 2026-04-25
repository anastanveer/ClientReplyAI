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

        if ($context->hasOptionalVariants()) {
            $lines[] = 'Generate optional variants only for the specifically requested variant keys — nothing extra.';
        }

        $lines[] = $this->jsonInstruction($context);

        return implode("\n", $lines);
    }

    protected function buildUserPrompt(ResolvedPromptContext $context): string
    {
        $sections = [
            '## TASK',
            'Rewrite the message below into a polished, human, outcome-driven reply. Apply full communication intelligence.',
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

        $sections[] = '';
        $sections[] = '## INTELLIGENCE CHECKLIST — apply silently before writing';
        $sections[] = '1. What is the sender\'s real intent? (get paid / maintain relationship / push strongly / de-escalate / inform / request)';
        $sections[] = '2. What type of receiver is this likely addressed to? Infer from context.';
        $sections[] = '3. What reply wording will best achieve the sender\'s outcome?';
        $sections[] = '4. Is the input weak or vague? If yes — elevate it using the use case. Do not ask questions.';
        $sections[] = '5. Does the reply feel human, confident, and natural? If not — rewrite.';

        if ($context->riskFlags !== []) {
            $sections[] = '';
            $sections[] = '## DETECTED WORDING RISKS';
            $sections[] = implode(', ', $context->riskFlags).'.';
            $sections[] = 'Rewrite to neutralize these risks while preserving the sender\'s intent and position.';
        }

        if ($context->hasOptionalVariants()) {
            $sections[] = '';
            $sections[] = '## VARIANTS REQUESTED';
            $sections[] = 'Also generate: '.$this->formatVariants($context);
        }

        $sections[] = '';
        $sections[] = '## OUTPUT RULES';
        $sections[] = '- Reply must be copy-paste ready with zero editing.';
        $sections[] = '- Sound human and purposeful — not templated, not robotic, not overly formal.';
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
