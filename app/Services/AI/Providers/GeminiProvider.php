<?php

namespace App\Services\AI\Providers;

use App\Services\AI\DTOs\ReplyGenerationRequest;
use App\Services\AI\DTOs\ReplyGenerationResult;
use App\Services\AI\Exceptions\ProviderRequestException;

class GeminiProvider extends AbstractProvider
{
    public function name(): string
    {
        return 'gemini';
    }

    public function generateReply(ReplyGenerationRequest $request): ReplyGenerationResult
    {
        $apiKey = $this->requireApiKey('api_key', 'GEMINI_API_KEY');
        $model = $this->resolveModel($request->model);
        $endpoint = rtrim((string) $this->config('endpoint'), '/').'/'.$model.':generateContent';

        $payload = [
            'contents' => $this->buildContents($request),
        ];

        if (filled($request->systemPrompt)) {
            $payload['system_instruction'] = [
                'parts' => [
                    ['text' => trim((string) $request->systemPrompt)],
                ],
            ];
        }

        $generationConfig = array_filter([
            'temperature' => $request->temperature,
            'maxOutputTokens' => $request->maxOutputTokens,
            'responseMimeType' => 'application/json',
        ], static fn (mixed $value): bool => $value !== null);

        if ($generationConfig !== []) {
            $payload['generationConfig'] = $generationConfig;
        }

        $response = $this->http()
            ->withHeaders(['x-goog-api-key' => $apiKey])
            ->post($endpoint, $payload);

        if ($response->failed()) {
            $this->throwForFailedResponse($response);
        }

        $data = $response->json();
        $text = $this->extractText($data);

        if ($text === '') {
            throw new ProviderRequestException($this->name(), 'Gemini returned an empty response.');
        }

        return new ReplyGenerationResult(
            provider: $this->name(),
            model: (string) data_get($data, 'modelVersion', $model),
            content: $text,
            finishReason: data_get($data, 'candidates.0.finishReason'),
            usage: [
                'input_tokens' => data_get($data, 'usageMetadata.promptTokenCount'),
                'output_tokens' => data_get($data, 'usageMetadata.candidatesTokenCount'),
                'total_tokens' => data_get($data, 'usageMetadata.totalTokenCount'),
            ],
            rawResponse: $data,
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function buildContents(ReplyGenerationRequest $request): array
    {
        return array_map(
            static fn (array $message): array => [
                'role' => $message['role'] === 'assistant' ? 'model' : 'user',
                'parts' => [
                    ['text' => $message['content']],
                ],
            ],
            $request->messages(),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function extractText(array $data): string
    {
        $parts = data_get($data, 'candidates.0.content.parts', []);

        if (! is_array($parts)) {
            return '';
        }

        $segments = [];

        foreach ($parts as $part) {
            // Gemini 2.5 Flash thinking model includes thought parts — skip them
            if (data_get($part, 'thought') === true) {
                continue;
            }

            $text = trim((string) data_get($part, 'text', ''));

            if ($text !== '') {
                $segments[] = $text;
            }
        }

        return trim(implode("\n", $segments));
    }
}
