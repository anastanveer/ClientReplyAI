<?php

namespace Tests\Unit\AIEngine;

use App\Services\AIEngine\IntentDetector;
use PHPUnit\Framework\TestCase;

class IntentDetectorTest extends TestCase
{
    private IntentDetector $detector;

    protected function setUp(): void
    {
        $this->detector = new IntentDetector();
    }

    public function test_detects_payment_follow_up(): void
    {
        $this->assertSame('payment_follow_up', $this->detector->detect('client has not paid the invoice yet', 'general_reply'));
        $this->assertSame('payment_follow_up', $this->detector->detect('pending payment needs to be cleared', 'general_reply'));
    }

    public function test_detects_negotiation(): void
    {
        $this->assertSame('negotiation', $this->detector->detect('the budget is very tight can you lower the price', 'general_reply'));
        $this->assertSame('negotiation', $this->detector->detect('I want to negotiate the rate', 'general_reply'));
    }

    public function test_detects_apology(): void
    {
        $this->assertSame('apology', $this->detector->detect('I want to apologize for the mistake I made', 'general_reply'));
        $this->assertSame('apology', $this->detector->detect('so sorry for this issue', 'general_reply'));
    }

    public function test_detects_delay_update(): void
    {
        $this->assertSame('delay_update', $this->detector->detect('the project is delayed and will take more time', 'general_reply'));
    }

    public function test_detects_follow_up(): void
    {
        $this->assertSame('follow_up', $this->detector->detect('just following up on my previous message', 'general_reply'));
        $this->assertSame('follow_up', $this->detector->detect('checking in to see if there is any update', 'general_reply'));
    }

    public function test_detects_complaint_response(): void
    {
        $this->assertSame('complaint_response', $this->detector->detect('client is unhappy and has a complaint', 'general_reply'));
    }

    public function test_detects_project_delivery(): void
    {
        $this->assertSame('project_delivery', $this->detector->detect('the project is now completed and delivered', 'general_reply'));
    }

    public function test_detects_recruiter_reply(): void
    {
        $this->assertSame('recruiter_reply', $this->detector->detect('recruiter reached out about hiring for this role', 'general_reply'));
    }

    public function test_falls_back_to_use_case_when_no_keyword_match(): void
    {
        $this->assertSame('payment_follow_up', $this->detector->detect('xyz abc def', 'payment_reminder'));
        $this->assertSame('general_professional', $this->detector->detect('xyz abc def', 'general_reply'));
    }
}
