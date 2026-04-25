<?php

namespace App\Services\AIEngine;

class AIEngineContext
{
    /**
     * @param list<string> $detectedRisks
     */
    public function __construct(
        public readonly string $intent,
        public readonly string $intentLabel,
        public readonly string $outcome,
        public readonly string $outcomeLabel,
        public readonly string $clientType,
        public readonly string $clientTypeLabel,
        public readonly array $detectedRisks,
        public readonly string $recommendedLength,
        public readonly bool $addNextStep,
        public readonly bool $shouldBeFirm,
        public readonly bool $shouldSoften,
        public readonly string $strategyNote,
        // New fields from extended engine modules
        public readonly string $ladderStage = 'soft',
        public readonly string $ladderLabel = 'First contact / polite opening',
        public readonly string $situationMode = 'standard',
        public readonly string $situationLabel = 'Standard professional exchange',
        public readonly string $platformStyle = 'general',
        public readonly string $intensitySignal = 'match_tone',
        public readonly ?string $contextHint = null,
    ) {}

    public function hasRisks(): bool
    {
        return $this->detectedRisks !== [];
    }

    public function toPromptSection(): string
    {
        $lines = [
            '## AI ENGINE ANALYSIS (pre-computed — use as authoritative context)',
            "Detected intent: {$this->intentLabel}",
            "Target outcome: {$this->outcomeLabel}",
            "Inferred receiver type: {$this->clientTypeLabel}",
            "Client situation: {$this->situationLabel}",
            "Escalation stage: {$this->ladderLabel}",
        ];

        if ($this->platformStyle !== 'general') {
            $lines[] = "Platform style: {$this->platformStyle}";
        }

        if ($this->strategyNote !== '') {
            $lines[] = "Reply strategy: {$this->strategyNote}";
        }

        $lines[] = "Recommended length: {$this->recommendedLength}";

        if ($this->contextHint !== null) {
            $lines[] = "Context signal: {$this->contextHint}";
        }

        if ($this->intensitySignal === 'de_escalate') {
            $lines[] = 'Intensity: de-escalate — lower the temperature, avoid confrontation.';
        } elseif ($this->intensitySignal === 'strengthen') {
            $lines[] = 'Intensity: strengthen — be more assertive, the sender is being too passive.';
        } elseif ($this->intensitySignal === 'firm_but_professional') {
            $lines[] = 'Intensity: firm — this is a final or near-final message, hold position firmly.';
        }

        if ($this->addNextStep) {
            $lines[] = 'Include a clear, actionable next step.';
        }

        if ($this->shouldBeFirm) {
            $lines[] = 'Be firm and confident — do not soften the core request or position.';
        }

        if ($this->shouldSoften) {
            $lines[] = 'Soften tone — reduce friction without losing the meaning.';
        }

        if ($this->detectedRisks !== []) {
            $lines[] = 'Wording risks to fix: '.implode('; ', $this->detectedRisks).'.';
        }

        return implode("\n", $lines);
    }
}
