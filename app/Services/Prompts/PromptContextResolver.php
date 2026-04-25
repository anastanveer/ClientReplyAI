<?php

namespace App\Services\Prompts;

use App\Services\Prompts\DTOs\ResolvedPromptContext;
use Illuminate\Support\Str;

class PromptContextResolver
{
    /**
     * @param  array<string, mixed>  $input
     */
    public function resolve(array $input): ResolvedPromptContext
    {
        $message = trim((string) ($input['message'] ?? $input['prompt'] ?? ''));

        $tone = $this->normalizeKey((string) ($input['tone'] ?? 'professional'));
        $useCase = $this->normalizeKey((string) ($input['use_case'] ?? 'general_reply'));
        $language = $this->normalizeKey((string) ($input['language'] ?? 'english_improvement'));
        $variants = $this->normalizeVariants($input['variants'] ?? []);

        return new ResolvedPromptContext(
            message: $message,
            additionalContext: $this->nullableString($input['context'] ?? $input['additional_context'] ?? null),
            goal: $this->nullableString($input['goal'] ?? null),
            receiver: $this->nullableString($input['receiver'] ?? null),
            platform: $this->nullableString($input['platform'] ?? null),
            tone: array_key_exists($tone, $this->toneRules()) ? $tone : 'professional',
            useCase: array_key_exists($useCase, $this->useCaseRules()) ? $useCase : 'general_reply',
            language: array_key_exists($language, $this->languageRules()) ? $language : 'english_improvement',
            requestedVariants: $variants,
            allowedVariants: array_values(array_intersect($variants, array_keys($this->variantRules()))),
            riskFlags: $this->detectRiskFlags($message),
            toneRule: $this->toneRules()[$tone] ?? $this->toneRules()['professional'],
            useCaseRule: $this->useCaseRules()[$useCase] ?? $this->useCaseRules()['general_reply'],
            languageRule: $this->languageRules()[$language] ?? $this->languageRules()['english_improvement'],
        );
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function toneRules(): array
    {
        return [
            'professional' => [
                'label' => 'Professional',
                'instruction' => 'Keep the reply polished, credible, and ready for work or client communication.',
            ],
            'friendly' => [
                'label' => 'Friendly',
                'instruction' => 'Keep the reply warm, human, and approachable without becoming casual or careless.',
            ],
            'polite' => [
                'label' => 'Polite',
                'instruction' => 'Use respectful wording, soften harsh edges, and keep the message courteous.',
            ],
            'confident' => [
                'label' => 'Confident',
                'instruction' => 'Sound assured and competent without arrogance or pressure.',
            ],
            'strong' => [
                'label' => 'Strong',
                'instruction' => 'Be firm and clear about boundaries or requests while staying professional.',
            ],
            'soft' => [
                'label' => 'Soft',
                'instruction' => 'Use gentle language and reduce friction while preserving the user meaning.',
            ],
            'direct' => [
                'label' => 'Direct',
                'instruction' => 'Keep the reply concise, straight to the point, and easy to act on.',
            ],
            'respectful' => [
                'label' => 'Respectful',
                'instruction' => 'Prioritize dignity, calm wording, and a balanced professional tone.',
            ],
            'short' => [
                'label' => 'Short',
                'instruction' => 'Keep the reply brief and efficient while retaining the core message.',
            ],
            'detailed' => [
                'label' => 'Detailed',
                'instruction' => 'Provide a fuller response only when necessary, still keeping it practical and readable.',
            ],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function useCaseRules(): array
    {
        return [
            'general_reply' => [
                'label' => 'General Reply',
                'instruction' => 'Write a broadly useful daily communication reply that is practical and natural.',
            ],
            'fiverr_client_reply' => [
                'label' => 'Fiverr Client Reply',
                'instruction' => 'Sound helpful, clear, and professional for a buyer or freelance client conversation.',
            ],
            'whatsapp_business_reply' => [
                'label' => 'WhatsApp Business Reply',
                'instruction' => 'Keep the reply compact, human, and easy to send in a business chat.',
            ],
            'email_reply' => [
                'label' => 'Email Reply',
                'instruction' => 'Use email-appropriate structure and wording, with clearer transitions and professionalism.',
            ],
            'job_recruiter_reply' => [
                'label' => 'Job Recruiter Reply',
                'instruction' => 'Sound professional, interested, and credible for recruiter communication.',
            ],
            'linkedin_reply' => [
                'label' => 'LinkedIn Reply',
                'instruction' => 'Keep the reply polished and networking-friendly without sounding generic.',
            ],
            'proposal_reply' => [
                'label' => 'Proposal Reply',
                'instruction' => 'Show clarity, seriousness, and value without exaggeration.',
            ],
            'complaint_reply' => [
                'label' => 'Complaint Reply',
                'instruction' => 'De-escalate tension, acknowledge the concern, and avoid defensive wording.',
            ],
            'apology_reply' => [
                'label' => 'Apology Reply',
                'instruction' => 'Take responsibility carefully, stay sincere, and avoid overpromising.',
            ],
            'follow_up_reply' => [
                'label' => 'Follow-up Reply',
                'instruction' => 'Be polite, timely, and clear about what the user is following up on.',
            ],
            'negotiation_reply' => [
                'label' => 'Negotiation Reply',
                'instruction' => 'Protect the user position with calm, firm, and practical wording.',
            ],
            'project_update_reply' => [
                'label' => 'Project Update Reply',
                'instruction' => 'Provide a clear update, next step, and realistic expectation.',
            ],
            'payment_reminder' => [
                'label' => 'Payment Reminder',
                'instruction' => 'Be firm but respectful about payment, avoiding desperation or hostility.',
            ],
            'delay_update' => [
                'label' => 'Delay Update',
                'instruction' => 'Explain the delay honestly, reduce friction, and avoid excuses.',
            ],
            'asking_for_requirements' => [
                'label' => 'Asking for Requirements',
                'instruction' => 'Ask clearly for the missing details needed to proceed efficiently.',
            ],
            'asking_for_access' => [
                'label' => 'Asking for Access',
                'instruction' => 'Request the required access clearly and professionally.',
            ],
            'review_request' => [
                'label' => 'Review Request',
                'instruction' => 'Ask for a review politely and naturally without pressure.',
            ],
            'support_response' => [
                'label' => 'Support Response',
                'instruction' => 'Be calm, practical, and helpful while giving the user confidence.',
            ],
            'friendly_daily_reply' => [
                'label' => 'Friendly Daily Reply',
                'instruction' => 'Keep it human and light while still improving clarity and tone.',
            ],
            'translation_improvement' => [
                'label' => 'Translation Improvement',
                'instruction' => 'Preserve meaning while improving clarity, grammar, and natural phrasing.',
            ],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function languageRules(): array
    {
        return [
            'english_improvement' => [
                'label' => 'English Improvement',
                'instruction' => 'Improve grammar, clarity, and tone while staying in natural English.',
            ],
            'broken_english_to_professional_english' => [
                'label' => 'Broken English to Professional English',
                'instruction' => 'Fix broken or awkward English and turn it into polished, professional English.',
            ],
            'roman_urdu_to_professional_english' => [
                'label' => 'Roman Urdu to Professional English',
                'instruction' => 'Convert Roman Urdu into clear, natural, professional English while preserving meaning.',
            ],
            'urdu_to_english' => [
                'label' => 'Urdu to English',
                'instruction' => 'Translate Urdu into natural, professional English without changing the core meaning.',
            ],
            'hindi_to_english' => [
                'label' => 'Hindi to English',
                'instruction' => 'Translate Hindi into natural, professional English without adding facts.',
            ],
            'english_to_german' => [
                'label' => 'English to German',
                'instruction' => 'Translate the final reply into clear, natural German while staying professional.',
            ],
            'english_to_french' => [
                'label' => 'English to French',
                'instruction' => 'Translate the final reply into clear, natural French while staying professional.',
            ],
            'english_to_arabic' => [
                'label' => 'English to Arabic',
                'instruction' => 'Translate the final reply into clear, natural Arabic while staying professional.',
            ],
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function variantRules(): array
    {
        return [
            'short_whatsapp_reply' => [
                'label' => 'Short WhatsApp Reply',
                'instruction' => 'Create a shorter version suitable for quick messaging apps.',
            ],
            'professional_email_reply' => [
                'label' => 'Professional Email Reply',
                'instruction' => 'Create a version that reads well as a professional email.',
            ],
            'friendly_human_reply' => [
                'label' => 'Friendly Human Reply',
                'instruction' => 'Create a version that sounds especially warm and human.',
            ],
            'strong_negotiation_reply' => [
                'label' => 'Strong Negotiation Reply',
                'instruction' => 'Create a firmer version for negotiation or boundary-setting.',
            ],
        ];
    }

    /**
     * @return list<string>
     */
    protected function normalizeVariants(mixed $variants): array
    {
        if (is_string($variants)) {
            $variants = array_filter(array_map('trim', explode(',', $variants)));
        }

        if (! is_array($variants)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(
            fn (mixed $variant): string => $this->normalizeKey((string) $variant),
            $variants,
        ))));
    }

    /**
     * @return list<string>
     */
    protected function detectRiskFlags(string $message): array
    {
        $normalized = Str::lower($message);
        $flags = [];

        if (Str::contains($normalized, ['asap', 'urgent', 'immediately', 'right now', 'today only'])) {
            $flags[] = 'urgency_pressure';
        }

        if (Str::contains($normalized, ['angry', 'hate', 'worst', 'ridiculous', 'nonsense', 'stupid', 'idiot'])) {
            $flags[] = 'hostile_tone';
        }

        if (Str::contains($normalized, ['guarantee', 'definitely', 'for sure', '100%', 'promise'])) {
            $flags[] = 'overpromise_risk';
        }

        if (Str::contains($normalized, ['legal action', 'court', 'sue', 'refund now or', 'police'])) {
            $flags[] = 'escalation_risk';
        }

        if (Str::contains($normalized, ['please please', 'beg', 'desperate', 'kindly please sir'])) {
            $flags[] = 'weak_positioning';
        }

        return array_values(array_unique($flags));
    }

    protected function normalizeKey(string $value): string
    {
        return str_replace('-', '_', Str::of($value)->lower()->snake()->value());
    }

    protected function nullableString(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }
}
