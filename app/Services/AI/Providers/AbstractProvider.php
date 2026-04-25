<?php

namespace App\Services\AI\Providers;

use App\Services\AI\AIProviderInterface;
use App\Services\AI\Exceptions\MissingApiKeyException;
use App\Services\AI\Exceptions\ProviderRequestException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

abstract class AbstractProvider implements AIProviderInterface
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(
        protected array $config = [],
    ) {}

    protected function config(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }

    protected function resolveModel(?string $override = null): string
    {
        return $override ?: (string) $this->config('model');
    }

    protected function http(): PendingRequest
    {
        return Http::acceptJson()
            ->contentType('application/json')
            ->timeout((int) config('ai.timeout', 20));
    }

    protected function requireApiKey(string $keyName = 'api_key', ?string $environmentVariable = null): string
    {
        $apiKey = trim((string) $this->config($keyName, ''));

        if ($apiKey === '') {
            throw new MissingApiKeyException($this->name(), $environmentVariable ?? strtoupper($this->name()).'_API_KEY');
        }

        return $apiKey;
    }

    protected function throwForFailedResponse(Response $response): never
    {
        $body = $response->json();

        $message = (string) data_get($body, 'error.message', $response->body());
        $message = trim($message) !== '' ? $message : 'Unexpected provider response.';

        throw new ProviderRequestException($this->name(), $message);
    }
}
