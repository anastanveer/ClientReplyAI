<?php

namespace App\Services\AIEngine;

class ReplyStrategyBuilder
{
    public function __construct(
        protected IntentDetector $intentDetector,
        protected OutcomeDetector $outcomeDetector,
        protected ClientPsychologyDetector $psychologyDetector,
        protected RiskDetector $riskDetector,
    ) {}

    public function build(
        string $message,
        string $useCase,
        string $tone,
        ?string $receiver = null,
        ?string $context = null,
    ): AIEngineContext {
        $intent = $this->intentDetector->detect($message, $useCase);
        $outcome = $this->outcomeDetector->detect($intent, $tone);
        $clientType = $this->psychologyDetector->detect($message, $receiver, $context);
        $risks = $this->riskDetector->detect($message);

        [$length, $addNextStep, $shouldBeFirm, $shouldSoften, $strategyNote] =
            $this->buildStrategy($intent, $outcome, $clientType, $tone);

        return new AIEngineContext(
            intent: $intent,
            intentLabel: $this->intentDetector->label($intent),
            outcome: $outcome,
            outcomeLabel: $this->outcomeDetector->label($outcome),
            clientType: $clientType,
            clientTypeLabel: $this->psychologyDetector->label($clientType),
            detectedRisks: $risks,
            recommendedLength: $length,
            addNextStep: $addNextStep,
            shouldBeFirm: $shouldBeFirm,
            shouldSoften: $shouldSoften,
            strategyNote: $strategyNote,
        );
    }

    /**
     * @return array{string, bool, bool, bool, string}
     */
    private function buildStrategy(
        string $intent,
        string $outcome,
        string $clientType,
        string $tone,
    ): array {
        $length = 'medium (2–3 sentences)';
        $addNextStep = false;
        $shouldBeFirm = false;
        $shouldSoften = false;
        $notes = [];

        // Intent-based guidance
        switch ($intent) {
            case 'payment_follow_up':
                $shouldBeFirm = true;
                $addNextStep = true;
                $notes[] = 'work is complete — signal that payment is overdue without begging or apologizing';
                break;

            case 'negotiation':
                $shouldBeFirm = true;
                $notes[] = 'hold value and position — signal quality and fairness without desperation';
                break;

            case 'apology':
                $shouldSoften = true;
                $addNextStep = true;
                $notes[] = 'take appropriate responsibility — avoid over-apologizing or making unsustainable promises';
                break;

            case 'delay_update':
                $addNextStep = true;
                $notes[] = 'honest about the delay — give revised expectation, maintain confidence';
                break;

            case 'follow_up':
                $notes[] = 'sender has been waiting — signal expectation politely without sounding needy';
                break;

            case 'complaint_response':
                $shouldSoften = true;
                $addNextStep = true;
                $notes[] = 'acknowledge immediately, zero defensiveness, clear path forward';
                break;

            case 'project_delivery':
                $addNextStep = true;
                $notes[] = 'confirm delivery confidently, invite review, set expectations for next step';
                break;
        }

        // Client psychology adjustments
        if ($clientType === 'ghosting') {
            $shouldBeFirm = true;
            $notes[] = 'receiver is unresponsive — be more direct, create mild urgency';
        }

        if ($clientType === 'angry') {
            $shouldSoften = true;
            $notes[] = 'receiver is upset — lead with acknowledgment before any other point';
        }

        if ($clientType === 'busy') {
            $length = 'short (1–2 sentences)';
            $notes[] = 'receiver is busy — keep it brief, scannable, and easy to act on';
        }

        if ($clientType === 'price_sensitive') {
            $notes[] = 'receiver is budget-conscious — justify value without discounting';
        }

        if ($clientType === 'corporate') {
            $notes[] = 'formal receiver — maintain professional register throughout';
        }

        // Tone overrides
        if (in_array(strtolower($tone), ['short', 'direct'])) {
            $length = 'short (1–2 sentences)';
        }

        if (strtolower($tone) === 'detailed') {
            $length = 'detailed (3–5 sentences)';
        }

        return [$length, $addNextStep, $shouldBeFirm, $shouldSoften, implode('; ', $notes)];
    }
}
