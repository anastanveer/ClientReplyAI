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
        ];

        if ($this->strategyNote !== '') {
            $lines[] = "Reply strategy: {$this->strategyNote}";
        }

        $lines[] = "Recommended length: {$this->recommendedLength}";

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
            $lines[] = 'Wording risks to fix: ' . implode('; ', $this->detectedRisks) . '.';
        }

        return implode("\n", $lines);
    }
}
