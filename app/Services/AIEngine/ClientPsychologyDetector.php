<?php

namespace App\Services\AIEngine;

use Illuminate\Support\Str;

class ClientPsychologyDetector
{
    public function detect(string $message, ?string $receiver, ?string $context): string
    {
        $combined = Str::lower($message . ' ' . ($receiver ?? '') . ' ' . ($context ?? ''));

        if ($this->matches($combined, ['no response', 'not responding', 'ignoring', 'ghosting', "haven't heard", 'still waiting', 'no reply', 'seen but not replied', 'left on read'])) {
            return 'ghosting';
        }

        if ($this->matches($combined, ['angry', 'very upset', 'furious', 'legal action', 'lawsuit', 'dispute', 'chargeback', 'refund or', 'unacceptable', 'worst service', 'terrible', 'disgusted'])) {
            return 'angry';
        }

        if ($this->matches($combined, ['corporate', 'manager', 'director', 'ceo', 'cto', 'formal', 'company policy', 'terms and conditions', 'contract', 'board'])) {
            return 'corporate';
        }

        if ($this->matches($combined, ['expensive', 'too much', 'budget', 'cheaper', 'can you reduce', 'lower price', 'affordable', 'cost too high', 'price is high', 'over budget'])) {
            return 'price_sensitive';
        }

        if ($this->matches($combined, ["don't understand", 'not sure', 'what do you mean', 'confused', 'clarify', 'can you explain', 'what exactly'])) {
            return 'confused';
        }

        if ($this->matches($combined, ['busy', 'quick', 'asap', 'immediately', 'no time', 'brief', 'short reply', 'make it quick'])) {
            return 'busy';
        }

        if ($this->matches($combined, ['thanks', 'appreciate', 'great work', 'love it', 'happy with', 'glad', 'wonderful', 'amazing'])) {
            return 'friendly';
        }

        return 'unknown';
    }

    public function label(string $type): string
    {
        return match ($type) {
            'ghosting'       => 'Ghosting / unresponsive — be more direct',
            'angry'          => 'Angry / escalated — prioritize tone first',
            'corporate'      => 'Corporate / formal — maintain professionalism',
            'price_sensitive'=> 'Price-sensitive — justify value clearly',
            'confused'       => 'Confused — lead with clarity',
            'busy'           => 'Busy — keep it brief and scannable',
            'friendly'       => 'Friendly / cooperative — match their warmth',
            default          => 'Unknown — default to professional and human',
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
