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
                'instruction' => 'Keep the reply polished, credible, and work-appropriate. Sound like someone with authority and experience — not stiff or bureaucratic.',
            ],
            'friendly' => [
                'label' => 'Friendly',
                'instruction' => 'Keep the reply warm, human, and approachable. Build rapport without becoming casual, sloppy, or unprofessional.',
            ],
            'polite' => [
                'label' => 'Polite',
                'instruction' => 'Use courteous wording that softens hard edges while still delivering the message clearly. Never let politeness make the sender sound weak.',
            ],
            'confident' => [
                'label' => 'Confident',
                'instruction' => 'Sound assured, grounded, and competent. Project quiet authority — not aggressive, not uncertain. The sender knows their value.',
            ],
            'strong' => [
                'label' => 'Strong',
                'instruction' => 'Be firm, clear, and unapologetic about the request or boundary while remaining professional. This is not aggression — it is clarity with backbone.',
            ],
            'soft' => [
                'label' => 'Soft',
                'instruction' => 'Use gentle, low-friction language that reduces resistance from the receiver. Preserve the full meaning while lowering emotional temperature.',
            ],
            'direct' => [
                'label' => 'Direct',
                'instruction' => 'Get to the point immediately. No filler, no padding. One or two sentences maximum. Easy to read and act on.',
            ],
            'respectful' => [
                'label' => 'Respectful',
                'instruction' => 'Prioritize the receiver\'s dignity and a calm, balanced tone. Suitable for sensitive situations or senior stakeholders.',
            ],
            'short' => [
                'label' => 'Short',
                'instruction' => 'Keep the reply as brief as possible while still being complete and professional. Aim for 1–2 sentences.',
            ],
            'detailed' => [
                'label' => 'Detailed',
                'instruction' => 'Provide a thorough response that covers the full situation — but still stays organized, readable, and not padded.',
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
                'instruction' => 'Write a practical, human, and natural daily communication reply. Detect the sender intent and produce a reply that fits the real situation.',
            ],
            'fiverr_client_reply' => [
                'label' => 'Fiverr Client Reply',
                'instruction' => 'Write for a freelance platform context. Sound professional, reliable, and responsive. Focus on maintaining the client relationship while asserting your expertise. Do not sound desperate or overly eager.',
            ],
            'whatsapp_business_reply' => [
                'label' => 'WhatsApp Business Reply',
                'instruction' => 'Keep the reply compact and conversational while staying professional. WhatsApp readers scan quickly — be clear and direct. Avoid long paragraphs.',
            ],
            'email_reply' => [
                'label' => 'Email Reply',
                'instruction' => 'Write with email-appropriate structure: clear opening, concise body, and a purposeful close. Use professional but non-stiff wording. Avoid excessive formality.',
            ],
            'job_recruiter_reply' => [
                'label' => 'Job Recruiter Reply',
                'instruction' => 'Sound genuinely interested, professional, and confident — not desperate. Show that you value the opportunity while positioning yourself as a strong candidate. Keep it concise and easy to respond to.',
            ],
            'linkedin_reply' => [
                'label' => 'LinkedIn Reply',
                'instruction' => 'Sound polished, human, and networking-appropriate. Avoid corporate jargon or hollow phrases. Keep it warm, purposeful, and brief enough to invite a response.',
            ],
            'proposal_reply' => [
                'label' => 'Proposal Reply',
                'instruction' => 'Show clarity, confidence, and clear value. Make it easy for the receiver to say yes. Do not exaggerate or overpromise. Signal that you understand their need and can deliver.',
            ],
            'complaint_reply' => [
                'label' => 'Complaint Reply',
                'instruction' => 'De-escalate tension immediately. Acknowledge the concern without being defensive or dismissive. Take ownership where appropriate. Offer a clear next step. Do not make promises you cannot keep.',
            ],
            'apology_reply' => [
                'label' => 'Apology Reply',
                'instruction' => 'Be genuinely sincere, not performative. Take appropriate responsibility without over-apologizing or making the sender look weak. Offer a clear path forward. Avoid vague language like "I\'m sorry you feel that way."',
            ],
            'follow_up_reply' => [
                'label' => 'Follow-up Reply',
                'instruction' => 'Write a confident, polite follow-up that signals the sender has been waiting but is not desperate. Be specific about what they are following up on. Make it easy for the receiver to respond.',
            ],
            'negotiation_reply' => [
                'label' => 'Negotiation Reply',
                'instruction' => 'Hold the sender\'s position with calm, strategic confidence. Signal value clearly without appearing desperate. Do not concede unnecessarily. Protect the relationship while pushing firmly toward the goal.',
            ],
            'project_update_reply' => [
                'label' => 'Project Update Reply',
                'instruction' => 'Provide a clear, factual status update with a concrete next step. Be honest about progress. Manage expectations proactively. Never make up details about completion or timelines.',
            ],
            'payment_reminder' => [
                'label' => 'Payment Reminder',
                'instruction' => 'Write a firm, dignified payment reminder. Signal that the sender has fulfilled their obligation and expects the same in return. Do not beg, apologize, or over-explain — that weakens positioning. A subtle urgency is acceptable but avoid aggression or desperation. Keep the door open to a positive response.',
            ],
            'delay_update' => [
                'label' => 'Delay Update',
                'instruction' => 'Communicate the delay honestly and concisely. Do not make excuses. Acknowledge the inconvenience, give a realistic revised expectation, and affirm commitment. Keep the receiver\'s trust intact.',
            ],
            'asking_for_requirements' => [
                'label' => 'Asking for Requirements',
                'instruction' => 'Ask clearly and efficiently for the specific information or materials needed to proceed. Frame it as enabling faster, better work. Avoid vague or open-ended requests.',
            ],
            'asking_for_access' => [
                'label' => 'Asking for Access',
                'instruction' => 'Request the necessary access clearly, professionally, and with a brief reason why it is needed. Make it easy for the receiver to act quickly.',
            ],
            'review_request' => [
                'label' => 'Review Request',
                'instruction' => 'Ask for a review in a natural, low-pressure way. Sound genuine and appreciative, not robotic or transactional. Make it easy for the receiver to take the action.',
            ],
            'support_response' => [
                'label' => 'Support Response',
                'instruction' => 'Be calm, empathetic, and practical. Give the user confidence that their issue is understood and being handled. Provide a clear action or solution. Avoid jargon or overly scripted support language.',
            ],
            'friendly_daily_reply' => [
                'label' => 'Friendly Daily Reply',
                'instruction' => 'Keep it warm, human, and natural while improving clarity and flow. Match a conversational but thoughtful register.',
            ],
            'translation_improvement' => [
                'label' => 'Translation Improvement',
                'instruction' => 'Preserve the exact meaning while improving naturalness, clarity, grammar, and professional register. Do not add or remove intent.',
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

        if (Str::contains($normalized, ['asap', 'urgent', 'immediately', 'right now', 'today only', 'by end of day', 'last chance'])) {
            $flags[] = 'urgency_pressure — soften the urgency without losing firmness';
        }

        if (Str::contains($normalized, ['angry', 'hate', 'worst', 'ridiculous', 'nonsense', 'stupid', 'idiot', 'pathetic', 'unacceptable', 'furious', 'disgusted'])) {
            $flags[] = 'hostile_tone — rewrite to professional assertiveness';
        }

        if (Str::contains($normalized, ['guarantee', 'definitely', 'for sure', '100%', 'promise', 'i will make sure', 'without fail', 'always'])) {
            $flags[] = 'overpromise_risk — replace with confident but non-binding language';
        }

        if (Str::contains($normalized, ['legal action', 'court', 'sue', 'lawyer', 'refund now or', 'police', 'dispute', 'chargeback'])) {
            $flags[] = 'escalation_risk — keep the door open for resolution before threats';
        }

        if (Str::contains($normalized, ['please please', 'beg', 'desperate', 'kindly please sir', 'i really need', 'so sorry to bother', 'hope you don\'t mind', 'if possible please'])) {
            $flags[] = 'weak_positioning — rewrite to confident, equal-footing language';
        }

        if (Str::contains($normalized, ['sorry for disturbing', 'sorry to bother', 'sorry if', 'i apologize for contacting', 'forgive me for'])) {
            $flags[] = 'unnecessary_apology — remove self-diminishing opener';
        }

        if (Str::contains($normalized, ['as per my last', 'as i said before', 'i already told you', 'how many times'])) {
            $flags[] = 'passive_aggression — rewrite to firm but professional tone';
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
