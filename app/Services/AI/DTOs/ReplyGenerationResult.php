<?php

namespace App\Services\AI\DTOs;

final readonly class ReplyGenerationResult
{
    /**
     * @param  array<string, int|float|null>  $usage
     * @param  array<string, mixed>  $rawResponse
     */
    public function __construct(
        public string $provider,
        public string $model,
        public string $content,
        public ?string $finishReason = null,
        public array $usage = [],
        public array $rawResponse = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'provider' => $this->provider,
            'model' => $this->model,
            'content' => $this->content,
            'finish_reason' => $this->finishReason,
            'usage' => $this->usage,
            'raw_response' => $this->rawResponse,
        ];
    }
}
