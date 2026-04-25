<?php

namespace Tests\Unit\AIEngine;

use App\Services\AIEngine\SituationDetector;
use PHPUnit\Framework\TestCase;

class SituationDetectorTest extends TestCase
{
    private SituationDetector $detector;

    protected function setUp(): void
    {
        $this->detector = new SituationDetector();
    }

    public function test_detects_angry_client(): void
    {
        $this->assertSame('angry_client', $this->detector->detect('this is absolutely unacceptable I am very upset', null, null));
        $this->assertSame('angry_client', $this->detector->detect('client is furious', 'angry client', null));
    }

    public function test_detects_ghosting(): void
    {
        $this->assertSame('ghosting', $this->detector->detect('they are not responding and ignoring my messages', null, null));
        $this->assertSame('ghosting', $this->detector->detect('client disappeared after I sent the work', null, null));
    }

    public function test_detects_low_budget(): void
    {
        $this->assertSame('low_budget', $this->detector->detect('the client says it is too expensive and wants a discount', null, null));
        $this->assertSame('low_budget', $this->detector->detect('they have a tight budget and cannot afford this rate', null, null));
    }

    public function test_detects_payment_due(): void
    {
        $this->assertSame('payment_due', $this->detector->detect('the invoice is overdue and payment is still pending', null, null));
    }

    public function test_detects_revision_request(): void
    {
        $this->assertSame('revision_request', $this->detector->detect('client wants a revision this is not what I wanted', null, null));
        $this->assertSame('revision_request', $this->detector->detect('please redo this section and modify the design', null, null));
    }

    public function test_detects_recruiter(): void
    {
        $this->assertSame('recruiter', $this->detector->detect('recruiter reached out about a job position interview', null, null));
    }

    public function test_detects_unclear_requirement(): void
    {
        $this->assertSame('unclear_requirement', $this->detector->detect('I am confused about what you mean can you clarify', null, null));
    }

    public function test_returns_standard_when_no_signals(): void
    {
        $this->assertSame('standard', $this->detector->detect('please send me the updated file', null, null));
    }

    public function test_uses_receiver_and_context_in_detection(): void
    {
        $this->assertSame('angry_client', $this->detector->detect('they complained', 'furious client', null));
        $this->assertSame('low_budget', $this->detector->detect('let us talk', null, 'client has tight budget cannot afford'));
    }
}
