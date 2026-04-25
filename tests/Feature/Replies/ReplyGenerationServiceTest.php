<?php

namespace Tests\Feature\Replies;

use App\Models\User;
use App\Services\AI\AIService;
use App\Services\AI\DTOs\ReplyGenerationResult;
use App\Services\Replies\Exceptions\InvalidAiResponseException;
use App\Services\Replies\ReplyGenerationService;
use App\Services\Usage\Exceptions\DailyLimitExceededException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class ReplyGenerationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_and_persists_chat_messages_and_usage(): void
    {
        $user = User::factory()->create([
            'plan' => 'free',
            'timezone' => 'UTC',
        ]);

        $aiService = Mockery::mock(AIService::class);
        $aiService->shouldReceive('generateReply')
            ->once()
            ->andReturn(new ReplyGenerationResult(
                provider: 'gemini',
                model: 'gemini-test-model',
                content: '{"best_reply":"Hi, just following up on the pending payment for the completed work.","risk_note":null,"variants":{}}',
                finishReason: 'STOP',
                usage: ['total_tokens' => 44],
                rawResponse: [],
            ));

        $this->app->instance(AIService::class, $aiService);

        $result = $this->app->make(ReplyGenerationService::class)->generate($user, [
            'message' => 'Please ask client again for payment',
            'tone' => 'Professional',
            'use_case' => 'Payment Reminder',
            'language' => 'English Improvement',
            'mode' => 'quick',
            'variants' => [],
        ]);

        $this->assertSame('gemini', $result->provider);
        $this->assertSame('Hi, just following up on the pending payment for the completed work.', $result->bestReply);
        $this->assertDatabaseCount('chats', 1);
        $this->assertDatabaseCount('chat_messages', 2);
        $this->assertSame(1, $user->usageLimits()->first()?->replies_generated);
        $this->assertDatabaseHas('chat_messages', [
            'chat_id' => $result->chat->id,
            'role' => 'assistant',
            'message_type' => 'generated_reply',
        ]);
    }

    public function test_it_throws_when_daily_limit_is_exceeded(): void
    {
        $user = User::factory()->create([
            'plan' => 'free',
            'timezone' => 'UTC',
        ]);

        $user->usageLimits()->create([
            'usage_date' => now('UTC')->toDateString(),
            'replies_generated' => 10,
            'saved_replies_count' => 0,
        ]);

        $this->expectException(DailyLimitExceededException::class);

        $this->app->make(ReplyGenerationService::class)->generate($user, [
            'message' => 'Please write a reply',
            'tone' => 'Professional',
            'use_case' => 'General Reply',
            'language' => 'English Improvement',
            'mode' => 'quick',
            'variants' => [],
        ]);
    }

    public function test_it_rejects_invalid_ai_json_payloads(): void
    {
        $user = User::factory()->create([
            'plan' => 'free',
            'timezone' => 'UTC',
        ]);

        $aiService = Mockery::mock(AIService::class);
        $aiService->shouldReceive('generateReply')
            ->once()
            ->andReturn(new ReplyGenerationResult(
                provider: 'gemini',
                model: 'gemini-test-model',
                content: 'not valid json',
                finishReason: 'STOP',
                usage: [],
                rawResponse: [],
            ));

        $this->app->instance(AIService::class, $aiService);

        $this->expectException(InvalidAiResponseException::class);

        $this->app->make(ReplyGenerationService::class)->generate($user, [
            'message' => 'Please write a reply',
            'tone' => 'Professional',
            'use_case' => 'General Reply',
            'language' => 'English Improvement',
            'mode' => 'quick',
            'variants' => [],
        ]);
    }
}
