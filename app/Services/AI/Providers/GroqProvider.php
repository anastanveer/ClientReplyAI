<?php

namespace App\Services\AI\Providers;

use App\Services\AI\DTOs\ReplyGenerationRequest;
use App\Services\AI\DTOs\ReplyGenerationResult;
use App\Services\AI\Exceptions\ProviderRequestException;

class GroqProvider extends AbstractProvider
{
    public function name(): string
    {
        return 'groq';
    }

    public function generateReply(ReplyGenerationRequest $request): ReplyGenerationResult
    {
        $apiKey = $this->requireApiKey('api_key', 'GROQ_API_KEY');
        $model = $this->resolveModel($request->model);

        $payload = array_filter([
            'model' => $model,
            'messages' => $this->buildMessages($request),
            'temperature' => $request->temperature,
            'max_completion_tokens' => $request->maxOutputTokens,
        ], static fn (mixed $value): bool => $value !== null);

        $response = $this->http()
            ->withToken($apiKey)
            ->post((string) $this->config('endpoint'), $payload);

        if ($response->failed()) {
            $this->throwForFailedResponse($response);
        }

        $data = $response->json();
        $text = trim((string) data_get($data, 'choices.0.message.content', ''));

        if ($text === '') {
            throw new ProviderRequestException($this->name(), 'Groq returned an empty response.');
        }

        return new ReplyGenerationResult(
            provider: $this->name(),
            model: (string) data_get($data, 'model', $model),
            content: $text,
            finishReason: data_get($data, 'choices.0.finish_reason'),
            usage: [
                'input_tokens' => data_get($data, 'usage.prompt_tokens'),
                'output_tokens' => data_get($data, 'usage.completion_tokens'),
                'total_tokens' => data_get($data, 'usage.total_tokens'),
            ],
            rawResponse: $data,
        );
    }

    /**
     * @return list<array{role:string, content:string}>
     */
    protected function buildMessages(ReplyGenerationRequest $request): array
    {
        $messages = [];

        if (filled($request->systemPrompt)) {
            $messages[] = [
                'role' => 'system',
                'content' => trim((string) $request->systemPrompt),
            ];
        }

        return [...$messages, ...$request->messages()];
    }
}
