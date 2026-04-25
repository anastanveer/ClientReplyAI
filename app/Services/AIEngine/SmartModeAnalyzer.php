<?php

namespace App\Services\AIEngine;

use Illuminate\Support\Str;

/**
 * Analyzes message content to auto-detect platform style and intensity signals.
 * Runs pure PHP — no API calls.
 */
class SmartModeAnalyzer
{
    public function analyze(string $message, ?string $platform, string $tone): SmartModeHints
    {
        $text = Str::lower($message);

        return new SmartModeHints(
            platformStyle: $this->detectPlatformStyle($text, $platform),
            intensitySignal: $this->detectIntensity($text, $tone),
            suggestedLength: $this->detectLength($text, $platform, $tone),
            contextHint: $this->buildContextHint($text),
        );
    }

    private function detectPlatformStyle(string $text, ?string $platform): string
    {
        $p = Str::lower($platform ?? '');

        if ($this->matches($p, ['whatsapp', 'wa ', 'chat'])) {
            return 'whatsapp';
        }
        if ($this->matches($p, ['email', 'gmail', 'outlook', 'mail'])) {
            return 'email';
        }
        if ($this->matches($p, ['linkedin', 'linked in'])) {
            return 'linkedin';
        }
        if ($this->matches($p, ['fiverr', 'upwork', 'freelance'])) {
            return 'freelance_platform';
        }

        // Infer from message content
        if ($this->matches($text, ['dear ', 'sincerely', 'regards', 'to whom it may concern'])) {
            return 'email';
        }
        if ($this->matches($text, ['hey ', 'hi there', 'yo ', 'bro ', 'buddy'])) {
            return 'chat';
        }

        return 'general';
    }

    private function detectIntensity(string $text, string $tone): string
    {
        // Strong signals override user's tone choice
        if ($this->matches($text, ['legal action', 'lawsuit', 'dispute', 'chargeback', 'refund or'])) {
            return 'de_escalate';
        }
        if ($this->matches($text, ['please please', 'beg', 'desperate', 'really need this', 'kindly sir'])) {
            return 'strengthen';
        }
        if ($this->matches($text, ['final notice', 'last time', 'forced to', 'no choice but'])) {
            return 'firm_but_professional';
        }

        return 'match_tone';
    }

    private function detectLength(string $text, ?string $platform, string $tone): string
    {
        $p = Str::lower($platform ?? '');

        if ($this->matches($p, ['whatsapp', 'wa '])) {
            return 'short';
        }
        if ($this->matches($p, ['email', 'gmail', 'outlook'])) {
            return 'medium';
        }
        if (in_array(strtolower($tone), ['short', 'direct'])) {
            return 'short';
        }
        if (strtolower($tone) === 'detailed') {
            return 'detailed';
        }

        // Short messages → short replies
        if (str_word_count($text) < 20) {
            return 'short';
        }

        return 'medium';
    }

    private function buildContextHint(string $text): ?string
    {
        if ($this->matches($text, ['payment', 'invoice', 'paid', 'dues'])) {
            return 'financial context — signal professionalism and firmness';
        }
        if ($this->matches($text, ['revision', 'change', 'update', 'modify', 'redo'])) {
            return 'revision request — be cooperative without appearing eager';
        }
        if ($this->matches($text, ['sorry', 'apologize', 'mistake', 'delay', 'late'])) {
            return 'damage control context — restore confidence';
        }
        if ($this->matches($text, ['job', 'position', 'role', 'recruiter', 'interview'])) {
            return 'career context — signal competence and interest';
        }

        return null;
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
