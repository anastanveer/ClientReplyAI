<?php

namespace Tests\Unit\AIEngine;

use App\Services\AIEngine\ReplyLadderDetector;
use PHPUnit\Framework\TestCase;

class ReplyLadderDetectorTest extends TestCase
{
    private ReplyLadderDetector $detector;

    protected function setUp(): void
    {
        $this->detector = new ReplyLadderDetector();
    }

    public function test_detects_soft_stage_for_first_contact(): void
    {
        $this->assertSame('soft', $this->detector->detect('I wanted to send you the invoice for the work done'));
        $this->assertSame('soft', $this->detector->detect('Hi, reaching out about the payment for the project'));
    }

    public function test_detects_balanced_stage_for_gentle_reminders(): void
    {
        $this->assertSame('balanced', $this->detector->detect('just following up on my previous message'));
        $this->assertSame('balanced', $this->detector->detect('checking in to see if you got my reminder about the invoice'));
        $this->assertSame('balanced', $this->detector->detect('this is a gentle reminder about the outstanding payment'));
    }

    public function test_detects_firm_stage_for_repeated_follow_ups(): void
    {
        $this->assertSame('firm', $this->detector->detect('I am still waiting and have not heard back after multiple times'));
        $this->assertSame('firm', $this->detector->detect('I have reached out several times with no response'));
        $this->assertSame('firm', $this->detector->detect('still haven received any payment'));
    }

    public function test_detects_final_stage_for_last_resort(): void
    {
        $this->assertSame('final', $this->detector->detect('this is my final notice before I take legal action'));
        $this->assertSame('final', $this->detector->detect('last reminder — unfortunately I have no choice but to escalate'));
        $this->assertSame('final', $this->detector->detect('I will have to escalate this if not resolved'));
    }

    public function test_labels_are_descriptive(): void
    {
        $this->assertStringContainsString('First contact', $this->detector->label('soft'));
        $this->assertStringContainsString('Follow-up', $this->detector->label('balanced'));
        $this->assertStringContainsString('Firm', $this->detector->label('firm'));
        $this->assertStringContainsString('Final', $this->detector->label('final'));
    }
}
