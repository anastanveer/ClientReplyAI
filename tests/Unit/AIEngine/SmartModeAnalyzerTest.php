<?php

namespace Tests\Unit\AIEngine;

use App\Services\AIEngine\SmartModeAnalyzer;
use PHPUnit\Framework\TestCase;

class SmartModeAnalyzerTest extends TestCase
{
    private SmartModeAnalyzer $analyzer;

    protected function setUp(): void
    {
        $this->analyzer = new SmartModeAnalyzer();
    }

    public function test_detects_whatsapp_platform_from_platform_string(): void
    {
        $hints = $this->analyzer->analyze('quick message', 'WhatsApp', 'professional');
        $this->assertSame('whatsapp', $hints->platformStyle);
        $this->assertSame('short', $hints->suggestedLength);
    }

    public function test_detects_email_platform_from_platform_string(): void
    {
        $hints = $this->analyzer->analyze('send an email', 'Gmail', 'professional');
        $this->assertSame('email', $hints->platformStyle);
        $this->assertSame('medium', $hints->suggestedLength);
    }

    public function test_infers_email_from_message_content(): void
    {
        $hints = $this->analyzer->analyze('Dear John, sincerely yours', null, 'professional');
        $this->assertSame('email', $hints->platformStyle);
    }

    public function test_infers_chat_from_message_content(): void
    {
        $hints = $this->analyzer->analyze('hey bro how are you doing', null, 'professional');
        $this->assertSame('chat', $hints->platformStyle);
    }

    public function test_detects_de_escalate_intensity(): void
    {
        $hints = $this->analyzer->analyze('I will take legal action and file a chargeback', null, 'professional');
        $this->assertSame('de_escalate', $hints->intensitySignal);
    }

    public function test_detects_strengthen_intensity(): void
    {
        $hints = $this->analyzer->analyze('please please I beg you I really need this done', null, 'professional');
        $this->assertSame('strengthen', $hints->intensitySignal);
    }

    public function test_detects_firm_intensity_for_final_notices(): void
    {
        $hints = $this->analyzer->analyze('this is my final notice I am forced to escalate', null, 'professional');
        $this->assertSame('firm_but_professional', $hints->intensitySignal);
    }

    public function test_detects_financial_context_hint(): void
    {
        $hints = $this->analyzer->analyze('the invoice payment is still pending and overdue', null, 'professional');
        $this->assertNotNull($hints->contextHint);
        $this->assertStringContainsString('financial', $hints->contextHint);
    }

    public function test_short_messages_get_short_length(): void
    {
        $hints = $this->analyzer->analyze('thanks', null, 'professional');
        $this->assertSame('short', $hints->suggestedLength);
    }

    public function test_detailed_tone_overrides_length(): void
    {
        $hints = $this->analyzer->analyze('please write a comprehensive reply about this situation', null, 'Detailed');
        $this->assertSame('detailed', $hints->suggestedLength);
    }

    public function test_returns_general_platform_when_no_signals(): void
    {
        $hints = $this->analyzer->analyze('please review this document', null, 'professional');
        $this->assertSame('general', $hints->platformStyle);
    }
}
