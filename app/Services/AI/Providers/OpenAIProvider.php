<?php

namespace App\Services\AI\Providers;

use App\Services\AI\DTOs\ReplyGenerationRequest;
use App\Services\AI\DTOs\ReplyGenerationResult;
use App\Services\AI\Exceptions\ProviderNotImplementedException;

class OpenAIProvider extends AbstractProvider
{
    public function name(): string
    {
        return 'openai';
    }

    public function generateReply(ReplyGenerationRequest $request): ReplyGenerationResult
    {
        throw new ProviderNotImplementedException($this->name());
    }
}
