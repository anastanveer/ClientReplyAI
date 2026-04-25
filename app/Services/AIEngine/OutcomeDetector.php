<?php

namespace App\Services\AIEngine;

class OutcomeDetector
{
    public function detect(string $intent, string $tone): string
    {
        $firmTones = ['strong', 'confident', 'direct'];

        return match ($intent) {
            'payment_follow_up'    => 'get_paid_faster',
            'negotiation'          => in_array(strtolower($tone), $firmTones) ? 'protect_position' : 'close_deal',
            'apology'              => 'restore_trust',
            'delay_update'         => 'manage_expectations',
            'follow_up'            => 'get_response',
            'complaint_response'   => 'de_escalate_and_resolve',
            'project_delivery'     => 'confirm_completion',
            'asking_for_something' => 'get_needed_information',
            'recruiter_reply'      => 'advance_opportunity',
            default                => 'communicate_professionally',
        };
    }

    public function label(string $outcome): string
    {
        return match ($outcome) {
            'get_paid_faster'        => 'Get paid — without damaging the relationship',
            'protect_position'       => 'Hold position firmly without aggression',
            'close_deal'             => 'Close or advance the deal',
            'restore_trust'          => 'Restore trust and repair the relationship',
            'manage_expectations'    => 'Manage expectations honestly and confidently',
            'get_response'           => 'Prompt a response without sounding desperate',
            'de_escalate_and_resolve'=> 'De-escalate tension and move toward resolution',
            'confirm_completion'     => 'Confirm delivery professionally and invite feedback',
            'get_needed_information' => 'Request information clearly and efficiently',
            'advance_opportunity'    => 'Advance the opportunity with confident interest',
            default                  => 'Communicate clearly and professionally',
        };
    }
}
