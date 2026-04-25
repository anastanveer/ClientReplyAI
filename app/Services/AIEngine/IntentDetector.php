<?php

namespace App\Services\AIEngine;

use Illuminate\Support\Str;

class IntentDetector
{
    public function detect(string $message, string $useCase): string
    {
        $text = Str::lower($message);

        if ($this->matches($text, ['payment', 'pay ', 'invoice', 'pending payment', 'clear payment', 'paid', 'amount due', 'outstanding balance', 'settle', 'dues'])) {
            return 'payment_follow_up';
        }

        if ($this->matches($text, ['negotiate', 'negotiation', 'budget', 'price', 'rate', 'offer', 'discount', 'lower', 'cheaper', 'reduce cost', 'too expensive', 'cost too'])) {
            return 'negotiation';
        }

        if ($this->matches($text, ['sorry', 'apologize', 'apology', 'my bad', 'my fault', 'i made a mistake', 'i was wrong', 'take responsibility'])) {
            return 'apology';
        }

        if ($this->matches($text, ['delay', 'delayed', 'late', 'behind schedule', 'postpone', 'push back', 'take more time', 'not ready yet', 'reschedule', 'overdue'])) {
            return 'delay_update';
        }

        if ($this->matches($text, ['follow up', 'following up', 'checking in', 'any update', 'heard back', 'still waiting', 'no response', 'wanted to check', 'just checking'])) {
            return 'follow_up';
        }

        if ($this->matches($text, ['unhappy', 'disappointed', 'issue', 'problem', 'wrong', 'not satisfied', 'not what', 'bad experience', 'frustrated', 'complaint'])) {
            return 'complaint_response';
        }

        if ($this->matches($text, ['delivered', 'completed', 'finished', 'done ', 'submitted', 'uploaded', 'sent over', 'here is the', 'please find'])) {
            return 'project_delivery';
        }

        if ($this->matches($text, ['need', 'require', 'please share', 'please send', 'send me', 'waiting for', 'can you send', 'need your', 'access to', 'credentials', 'login'])) {
            return 'asking_for_something';
        }

        if ($this->matches($text, ['job ', 'position', 'role', 'opportunity', 'interview', 'application', 'resume', ' cv ', 'hiring', 'recruiter', 'job offer'])) {
            return 'recruiter_reply';
        }

        return $this->fromUseCase($useCase);
    }

    private function fromUseCase(string $useCase): string
    {
        $map = [
            'payment_reminder'       => 'payment_follow_up',
            'negotiation_reply'      => 'negotiation',
            'apology_reply'          => 'apology',
            'delay_update'           => 'delay_update',
            'follow_up_reply'        => 'follow_up',
            'complaint_reply'        => 'complaint_response',
            'job_recruiter_reply'    => 'recruiter_reply',
            'asking_for_requirements'=> 'asking_for_something',
            'asking_for_access'      => 'asking_for_something',
            'project_update_reply'   => 'project_delivery',
            'proposal_reply'         => 'negotiation',
        ];

        $normalized = str_replace([' ', '-'], '_', Str::lower($useCase));

        return $map[$normalized] ?? 'general_professional';
    }

    public function label(string $intent): string
    {
        return match ($intent) {
            'payment_follow_up'   => 'Payment follow-up / invoice reminder',
            'negotiation'         => 'Negotiation / price discussion',
            'apology'             => 'Apology / taking responsibility',
            'delay_update'        => 'Delay / timeline update',
            'follow_up'           => 'Follow-up / checking in',
            'complaint_response'  => 'Complaint / issue response',
            'project_delivery'    => 'Project delivery / submission',
            'asking_for_something'=> 'Requesting information or access',
            'recruiter_reply'     => 'Recruiter / job opportunity reply',
            default               => 'General professional communication',
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
