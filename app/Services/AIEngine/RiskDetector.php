<?php

namespace App\Services\AIEngine;

use Illuminate\Support\Str;

class RiskDetector
{
    /**
     * @return list<string>
     */
    public function detect(string $message): array
    {
        $text = Str::lower($message);
        $risks = [];

        if ($this->matches($text, ['asap', 'urgent', 'immediately', 'right now', 'today only', 'last chance', 'by end of day', 'do it now'])) {
            $risks[] = 'urgency_pressure — rewrite to confident expectation';
        }

        if ($this->matches($text, ['angry', 'hate this', 'worst', 'ridiculous', 'nonsense', 'stupid', 'idiot', 'pathetic', 'furious', 'disgusted', 'unacceptable'])) {
            $risks[] = 'hostile_tone — rewrite to assertive professionalism';
        }

        if ($this->matches($text, ['guarantee', 'definitely', 'for sure', '100%', 'i promise', 'i will make sure', 'without fail', 'always deliver', 'never fail'])) {
            $risks[] = 'overpromise — use confident but non-binding language';
        }

        if ($this->matches($text, ['legal action', 'court', 'sue you', 'lawyer', 'refund now or', 'police', 'dispute', 'chargeback'])) {
            $risks[] = 'escalation_threat — offer resolution path before any threat language';
        }

        if ($this->matches($text, ['please please', 'beg', 'desperate', 'kindly please sir', 'i really need this', "if you don't mind", 'if possible please'])) {
            $risks[] = 'weak_positioning — rewrite as equal-footing professional';
        }

        if ($this->matches($text, ['sorry for disturbing', 'sorry to bother', 'sorry if', 'i apologize for contacting', 'forgive me for messaging'])) {
            $risks[] = 'unnecessary_apology — remove self-diminishing opener';
        }

        if ($this->matches($text, ['as per my last', 'as i said before', 'i already told you', 'how many times', 'once again i'])) {
            $risks[] = 'passive_aggression — rewrite to firm but neutral tone';
        }

        return array_values(array_unique($risks));
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
