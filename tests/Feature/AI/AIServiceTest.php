<?php

namespace Tests\Feature\AI;

use App\Services\AI\AIService;
use App\Services\AI\DTOs\ReplyGenerationRequest;
use App\Services\AI\Exceptions\ProviderAccessDeniedException;
use App\Services\AI\Exceptions\ProvidersExhaustedException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AIServiceTest extends TestCase
{
    public function test_it_uses_the_configured_gemini_provider(): void
    {
        config()->set('ai.default_provider', 'gemini');
        config()->set('ai.fallback_provider', 'groq');
        config()->set('ai.providers.gemini.api_key', 'gemini-test-key');
        config()->set('ai.providers.gemini.model', 'gemini-test-model');
        app()->forgetInstance(AIService::class);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'modelVersion' => 'gemini-test-model',
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Polished Gemini reply'],
                            ],
                        ],
                        'finishReason' => 'STOP',
                    ],
                ],
                'usageMetadata' => [
                    'promptTokenCount' => 20,
                    'candidatesTokenCount' => 15,
                    'totalTokenCount' => 35,
                ],
            ]),
        ]);

        $service = app(AIService::class);

        $result = $service->generateReply(new ReplyGenerationRequest(
            prompt: 'Write a client reply',
            systemPrompt: 'You are a communication assistant.',
        ));

        $this->assertSame('gemini', $result->provider);
        $this->assertSame('gemini-test-model', $result->model);
        $this->assertSame('Polished Gemini reply', $result->content);
        $this->assertSame(35, $result->usage['total_tokens']);
    }

    public function test_it_can_override_the_provider_to_groq(): void
    {
        config()->set('ai.default_provider', 'gemini');
        config()->set('ai.fallback_provider', 'groq');
        config()->set('ai.providers.groq.api_key', 'groq-test-key');
        config()->set('ai.providers.groq.model', 'llama-test-model');
        app()->forgetInstance(AIService::class);

        Http::fake([
            'api.groq.com/*' => Http::response([
                'model' => 'llama-test-model',
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Groq reply variant',
                        ],
                        'finish_reason' => 'stop',
                    ],
                ],
                'usage' => [
                    'prompt_tokens' => 12,
                    'completion_tokens' => 18,
                    'total_tokens' => 30,
                ],
            ]),
        ]);

        $service = app(AIService::class);

        $result = $service->generateReply(new ReplyGenerationRequest(
            prompt: 'Write a recruiter reply',
            provider: 'groq',
        ));

        $this->assertSame('groq', $result->provider);
        $this->assertSame('Groq reply variant', $result->content);
        $this->assertSame(30, $result->usage['total_tokens']);
    }

    public function test_it_falls_back_to_groq_when_gemini_is_missing(): void
    {
        config()->set('ai.default_provider', 'gemini');
        config()->set('ai.fallback_provider', 'groq');
        config()->set('ai.providers.gemini.api_key', null);
        config()->set('ai.providers.groq.api_key', 'groq-test-key');
        config()->set('ai.providers.groq.model', 'llama-fallback-model');
        app()->forgetInstance(AIService::class);

        Http::fake([
            'api.groq.com/*' => Http::response([
                'model' => 'llama-fallback-model',
                'choices' => [
                    [
                        'message' => [
                            'content' => 'Fallback Groq reply',
                        ],
                        'finish_reason' => 'stop',
                    ],
                ],
                'usage' => [
                    'prompt_tokens' => 10,
                    'completion_tokens' => 16,
                    'total_tokens' => 26,
                ],
            ]),
        ]);

        $service = app(AIService::class);

        $result = $service->generateReply(new ReplyGenerationRequest(prompt: 'Hello'));

        $this->assertSame('groq', $result->provider);
        $this->assertSame('Fallback Groq reply', $result->content);
    }

    public function test_it_throws_when_all_providers_fail(): void
    {
        config()->set('ai.default_provider', 'gemini');
        config()->set('ai.fallback_provider', 'groq');
        config()->set('ai.providers.gemini.api_key', null);
        config()->set('ai.providers.groq.api_key', null);
        app()->forgetInstance(AIService::class);

        $service = app(AIService::class);

        try {
            $service->generateReply(new ReplyGenerationRequest(prompt: 'Hello'));
            $this->fail('Expected a ProvidersExhaustedException to be thrown.');
        } catch (ProvidersExhaustedException $exception) {
            $this->assertStringContainsString('gemini', $exception->getMessage());
            $this->assertStringContainsString('groq', $exception->getMessage());
        }
    }

    public function test_openai_is_restricted_to_premium_or_testing(): void
    {
        config()->set('ai.providers.openai.api_key', 'openai-test-key');
        app()->forgetInstance(AIService::class);

        $service = app(AIService::class);

        try {
            $service->generateReply(new ReplyGenerationRequest(
                prompt: 'Testing OpenAI gate',
                provider: 'openai',
                metadata: ['plan' => 'free'],
            ));
            $this->fail('Expected a ProviderAccessDeniedException to be thrown.');
        } catch (ProviderAccessDeniedException $exception) {
            $this->assertStringContainsString('premium', $exception->userMessage());
            $this->assertStringContainsString('testing', $exception->userMessage());
        }
    }
}
