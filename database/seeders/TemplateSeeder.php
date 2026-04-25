<?php

namespace Database\Seeders;

use App\Models\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Polite Payment Reminder',
                'slug' => 'polite-payment-reminder',
                'category' => 'Payments',
                'use_case' => 'Payment Reminder',
                'tone' => 'Polite',
                'language' => 'English Improvement',
                'prompt_hint' => 'Use when payment is delayed and the user wants to stay respectful but firm.',
                'content' => 'Hi, just following up on the pending payment for the completed work. I would appreciate it if this could be processed today. Please let me know if you need anything from my side.',
            ],
            [
                'name' => 'Fiverr Delivery Follow-Up',
                'slug' => 'fiverr-delivery-follow-up',
                'category' => 'Freelancing',
                'use_case' => 'Fiverr Client Reply',
                'tone' => 'Professional',
                'language' => 'English Improvement',
                'prompt_hint' => 'Useful after delivery when the buyer is silent or unclear.',
                'content' => 'Hi, I wanted to check in regarding the delivered work. If you need any small adjustments, feel free to share them and I will review them promptly.',
            ],
            [
                'name' => 'Recruiter Availability Reply',
                'slug' => 'recruiter-availability-reply',
                'category' => 'Jobs',
                'use_case' => 'Job Recruiter Reply',
                'tone' => 'Professional',
                'language' => 'English Improvement',
                'prompt_hint' => 'Use when a recruiter asks for interview availability.',
                'content' => 'Thank you for reaching out. I am available for a conversation and would be happy to coordinate a suitable time. Please share the available slots and I will confirm promptly.',
            ],
            [
                'name' => 'WhatsApp Business Clarification',
                'slug' => 'whatsapp-business-clarification',
                'category' => 'Business',
                'use_case' => 'WhatsApp Business Reply',
                'tone' => 'Friendly',
                'language' => 'English Improvement',
                'prompt_hint' => 'Use when the customer message is unclear and you need missing details.',
                'content' => 'Thanks for your message. To help you properly, could you please share a few more details about what you need and your expected timeline?',
            ],
            [
                'name' => 'Project Delay Update',
                'slug' => 'project-delay-update',
                'category' => 'Project Updates',
                'use_case' => 'Delay Update',
                'tone' => 'Respectful',
                'language' => 'English Improvement',
                'prompt_hint' => 'Use when the user needs to explain a delay without sounding careless.',
                'content' => 'I wanted to give you a quick update. The task is taking slightly longer than expected, and I need a bit more time to deliver it properly. I appreciate your patience and will keep you updated on progress.',
            ],
            [
                'name' => 'Requirements Request',
                'slug' => 'requirements-request',
                'category' => 'Project Intake',
                'use_case' => 'Asking for Requirements',
                'tone' => 'Professional',
                'language' => 'English Improvement',
                'prompt_hint' => 'Use when the user needs the client to provide missing details.',
                'content' => 'Before I proceed, could you please share the full requirements, any reference material, and your expected deadline? That will help me respond accurately and avoid delays.',
            ],
            [
                'name' => 'Complaint Response',
                'slug' => 'complaint-response',
                'category' => 'Support',
                'use_case' => 'Complaint Reply',
                'tone' => 'Soft',
                'language' => 'English Improvement',
                'prompt_hint' => 'Use when a customer is upset and the user needs a calm response.',
                'content' => 'Thank you for sharing this feedback. I understand your concern, and I am sorry for the frustration this caused. I am reviewing the issue and will work on the best possible resolution.',
            ],
            [
                'name' => 'Roman Urdu to English Upgrade',
                'slug' => 'roman-urdu-to-english-upgrade',
                'category' => 'Language',
                'use_case' => 'Translation Improvement',
                'tone' => 'Professional',
                'language' => 'Roman Urdu to Professional English',
                'prompt_hint' => 'Use when the original input is informal Roman Urdu and needs polished English.',
                'content' => 'Please convert this Roman Urdu message into clear, natural, professional English while keeping the original meaning intact.',
            ],
        ];

        foreach ($templates as $template) {
            Template::updateOrCreate(
                ['slug' => $template['slug']],
                $template + ['is_system' => true],
            );
        }
    }
}
