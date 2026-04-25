<?php

namespace App\Services\AIEngine;

use Illuminate\Support\Str;

/**
 * Detects the client situation mode from message content and receiver hints.
 * Maps to a strategic communication posture.
 */
class SituationDetector
{
    public function detect(string $message, ?string $receiver, ?string $context): string
    {
        $text = Str::lower($message);
        $recv = Str::lower($receiver ?? '');
        $ctx  = Str::lower($context ?? '');
        $all  = $text.' '.$recv.' '.$ctx;

        if ($this->matches($all, ['angry', 'furious', 'unacceptable', 'disgusted', 'very upset', 'extremely unhappy', 'livid', 'terrible experience', 'worst', 'outraged'])) {
            return 'angry_client';
        }

        if ($this->matches($all, ['no response', 'not responding', 'not replying', 'ghosting', 'disappeared', 'ignoring', 'seen and ignored', 'left on read', 'not getting back'])) {
            return 'ghosting';
        }

        if ($this->matches($all, ['budget', 'too expensive', 'can you do cheaper', 'lower price', 'reduce rate', 'tight budget', 'expensive', 'not affordable', 'discount', 'negotiate price'])) {
            return 'low_budget';
        }

        if ($this->matches($all, ['payment', 'invoice', 'pending payment', 'amount due', 'paid', 'outstanding', 'overdue', 'dues', 'settle', 'transfer'])) {
            return 'payment_due';
        }

        if ($this->matches($all, ['revision', 'change', 'redo', 'modify', 'update this', 'not what i wanted', 'different from', 'please change', 'not happy with', 'fix this'])) {
            return 'revision_request';
        }

        if ($this->matches($all, ['job', 'interview', 'recruiter', 'position', 'hiring', 'opportunity', 'resume', ' cv ', 'applied', 'application'])) {
            return 'recruiter';
        }

        if ($this->matches($all, ['unclear', 'confused', 'not sure what', 'what do you mean', "don't understand", 'can you explain', 'vague', 'more details', 'clarify'])) {
            return 'unclear_requirement';
        }

        return 'standard';
    }

    public function label(string $situation): string
    {
        return match ($situation) {
            'angry_client'       => 'Angry / upset client — de-escalate first',
            'ghosting'           => 'Ghosting / unresponsive receiver — be more direct',
            'low_budget'         => 'Budget-sensitive receiver — justify value',
            'payment_due'        => 'Payment pending — firm but professional',
            'revision_request'   => 'Revision / change request — cooperative confidence',
            'recruiter'          => 'Recruiter / job context — signal competence',
            'unclear_requirement'=> 'Unclear requirements — ask with specificity',
            default              => 'Standard professional exchange',
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
