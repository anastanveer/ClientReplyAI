<?php

namespace App\Services\AI;

use App\Services\AI\DTOs\ReplyGenerationRequest;
use App\Services\AI\DTOs\ReplyGenerationResult;
use App\Services\AI\Exceptions\AIException;
use App\Services\AI\Exceptions\ProviderAccessDeniedException;
use App\Services\AI\Exceptions\ProvidersExhaustedException;
use App\Services\AI\Exceptions\UnsupportedProviderException;
use App\Services\AI\Providers\ClaudeProvider;
use App\Services\AI\Providers\GeminiProvider;
use App\Services\AI\Providers\GroqProvider;
use App\Services\AI\Providers\OllamaProvider;
use App\Services\AI\Providers\OpenAIProvider;

class AIService
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        protected array $config = [],
    ) {}

    public function generateReply(ReplyGenerationRequest $request): ReplyGenerationResult
    {
        $providers = $this->providerSequence($request);
        $failures = [];

        foreach ($providers as $providerName) {
            try {
                $this->guardProviderAccess($providerName, $request);

                return $this->provider($providerName)->generateReply($request);
            } catch (AIException $exception) {
                if (count($providers) === 1) {
                    throw $exception;
                }

                $failures[] = sprintf('%s: %s', $providerName, $exception->getMessage());
            }
        }

        throw new ProvidersExhaustedException($providers, $failures);
    }

    public function provider(?string $provider = null): AIProviderInterface
    {
        $provider = strtolower($provider ?: (string) data_get($this->config, 'default_provider', 'gemini'));
        $providerConfig = (array) data_get($this->config, "providers.{$provider}", []);

        return match ($provider) {
            'gemini' => new GeminiProvider($providerConfig),
            'groq' => new GroqProvider($providerConfig),
            'claude' => new ClaudeProvider($providerConfig),
            'openai' => new OpenAIProvider($providerConfig),
            'ollama' => new OllamaProvider($providerConfig),
            default => throw new UnsupportedProviderException($provider),
        };
    }

    /**
     * @return list<string>
     */
    public function supportedProviders(): array
    {
        return ['gemini', 'groq', 'claude', 'openai', 'ollama'];
    }

    /**
     * @return list<string>
     */
    protected function providerSequence(ReplyGenerationRequest $request): array
    {
        $requestedProvider = $request->provider ? strtolower($request->provider) : null;

        if ($requestedProvider !== null && $requestedProvider !== '') {
            return [$requestedProvider];
        }

        $primary = strtolower((string) data_get($this->config, 'default_provider', 'gemini'));
        $fallback = strtolower((string) data_get($this->config, 'fallback_provider', 'groq'));

        return array_values(array_unique(array_filter([$primary, $fallback])));
    }

    protected function guardProviderAccess(string $provider, ReplyGenerationRequest $request): void
    {
        $allowedScopes = (array) data_get($this->config, "provider_access.{$provider}", []);

        if ($allowedScopes === []) {
            return;
        }

        $activeScopes = $this->activeScopes($request);

        if (array_intersect($allowedScopes, $activeScopes) !== []) {
            return;
        }

        throw new ProviderAccessDeniedException($provider, $allowedScopes);
    }

    /**
     * @return list<string>
     */
    protected function activeScopes(ReplyGenerationRequest $request): array
    {
        $scopes = ['free'];

        $plan = strtolower((string) data_get($request->metadata, 'plan', ''));

        if (in_array($plan, ['pro', 'premium'], true)) {
            $scopes[] = 'premium';
        }

        if ((bool) data_get($request->metadata, 'testing', false)) {
            $scopes[] = 'testing';
        }

        return array_values(array_unique($scopes));
    }
}
