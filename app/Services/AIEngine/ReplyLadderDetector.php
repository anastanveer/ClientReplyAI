<?php

namespace App\Services\AIEngine;

use Illuminate\Support\Str;

/**
 * Detects how far along the escalation ladder a message is.
 * Used for payment follow-ups, ghosting, and complaint chains.
 */
class ReplyLadderDetector
{
    public function detect(string $message): string
    {
        $text = Str::lower($message);

        if ($this->matches($text, ['final notice', 'last reminder', 'unfortunately forced', 'no choice but', 'will have to escalate', 'legal action', 'chargeback', 'dispute', 'lawsuit', 'final warning'])) {
            return 'final';
        }

        if ($this->matches($text, ['still haven', 'still waiting', 'third time', 'again asking', 'multiple times', 'repeatedly', 'no response again', 'keep following up', 'reminded you', 'reached out several'])) {
            return 'firm';
        }

        if ($this->matches($text, ['follow up', 'following up', 'checking in', 'just wanted to', 'any update', 'gentle reminder', 'second reminder', 'wanted to check', 'just checking', 'reminder about'])) {
            return 'balanced';
        }

        return 'soft';
    }

    public function label(string $stage): string
    {
        return match ($stage) {
            'soft'     => 'First contact / polite opening',
            'balanced' => 'Follow-up / gentle reminder',
            'firm'     => 'Firm escalation / repeated follow-up',
            'final'    => 'Final notice / last resort',
            default    => 'Standard',
        };
    }

    /** @param list<string> $keywords */
    private function matches(string $text, array $keywords): bool
    {
        foreach ($keywords as $kw) {
            if (str_contains($text, $kw)) {
                return true;
            }
        }

        return false;
    }
}
