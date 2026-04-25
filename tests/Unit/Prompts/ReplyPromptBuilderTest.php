<?php

namespace Tests\Unit\Prompts;

use App\Services\Prompts\ReplyPromptBuilder;
use Tests\TestCase;

class ReplyPromptBuilderTest extends TestCase
{
    protected ReplyPromptBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();

        // Resolve via container — ReplyStrategyBuilder has its own dependency chain
        $this->builder = app(ReplyPromptBuilder::class);
    }

    public function test_default_prompt_builds_correct_structure(): void
    {
        $prompt = $this->builder->build([
            'message' => 'client not paying and i want ask again politely',
            'tone' => 'polite',
            'use_case' => 'payment_reminder',
            'language' => 'broken_english_to_professional_english',
        ]);

        $this->assertStringContainsString('first-person voice of the sender', $prompt->systemPrompt);
        $this->assertStringContainsString('Rewrite the message below', $prompt->userPrompt);
        $this->assertSame([], $prompt->variants);
        $this->assertInstanceOf(\stdClass::class, $prompt->responseSchema['variants']);
        $this->assertSame([], get_object_vars($prompt->responseSchema['variants']));
        $this->assertArrayHasKey('coach_note', $prompt->responseSchema);
        $this->assertArrayHasKey('next_step', $prompt->responseSchema);
    }

    public function test_optional_variants_are_only_added_when_requested(): void
    {
        $prompt = $this->builder->build([
            'message' => 'Need recruiter reply',
            'use_case' => 'job_recruiter_reply',
            'variants' => ['short_whatsapp_reply', 'professional_email_reply', 'invalid_variant'],
        ]);

        $this->assertSame(['short_whatsapp_reply', 'professional_email_reply'], $prompt->variants);
        $this->assertStringContainsString('Generate optional variants only for the specifically requested variant keys', $prompt->systemPrompt);
        $this->assertStringContainsString('Also generate:', $prompt->userPrompt);
        $this->assertArrayHasKey('short_whatsapp_reply', $prompt->responseSchema['variants']);
        $this->assertArrayHasKey('professional_email_reply', $prompt->responseSchema['variants']);
    }

    public function test_language_support_includes_roman_urdu_instruction(): void
    {
        $prompt = $this->builder->build([
            'message' => 'ap payment aj release kr dein please',
            'language' => 'roman_urdu_to_professional_english',
        ]);

        $this->assertStringContainsString('Roman Urdu', $prompt->systemPrompt);
        $this->assertStringContainsString('Roman Urdu to Professional English', $prompt->userPrompt);
    }

    public function test_prompt_includes_safety_guidance_when_risks_are_detected(): void
    {
        $prompt = $this->builder->build([
            'message' => 'This is ridiculous. You promised 100% and I need it right now.',
        ]);

        $this->assertNotEmpty($prompt->riskFlags);
        $this->assertStringContainsString('risk_note', $prompt->systemPrompt);
    }

    public function test_json_output_instruction_is_explicit(): void
    {
        $prompt = $this->builder->build([
            'message' => 'write a clean reply',
        ]);

        $this->assertStringContainsString('Return valid minified JSON', $prompt->systemPrompt);
        $this->assertStringContainsString('Return JSON only', $prompt->userPrompt);
        $this->assertArrayHasKey('best_reply', $prompt->responseSchema);
        $this->assertArrayHasKey('risk_note', $prompt->responseSchema);
        $this->assertArrayHasKey('coach_note', $prompt->responseSchema);
        $this->assertArrayHasKey('next_step', $prompt->responseSchema);
        $this->assertArrayHasKey('variants', $prompt->responseSchema);
    }

    public function test_ai_engine_analysis_block_is_injected_into_user_prompt(): void
    {
        $prompt = $this->builder->build([
            'message' => 'I have sent the invoice 3 times and still no payment',
            'use_case' => 'payment_reminder',
            'tone' => 'professional',
        ]);

        $this->assertStringContainsString('AI ENGINE ANALYSIS', $prompt->userPrompt);
        $this->assertStringContainsString('Detected intent:', $prompt->userPrompt);
        $this->assertStringContainsString('Target outcome:', $prompt->userPrompt);
    }

    public function test_coach_note_and_next_step_instructions_are_in_system_prompt(): void
    {
        $prompt = $this->builder->build([
            'message' => 'just following up on the payment',
        ]);

        $this->assertStringContainsString('coach_note', $prompt->systemPrompt);
        $this->assertStringContainsString('next_step', $prompt->systemPrompt);
    }
}
