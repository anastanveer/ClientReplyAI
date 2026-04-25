<?php

namespace App\Services\AI\Providers;

use App\Services\AI\DTOs\ReplyGenerationRequest;
use App\Services\AI\DTOs\ReplyGenerationResult;
use App\Services\AI\Exceptions\ProviderNotImplementedException;

class ClaudeProvider extends AbstractProvider
{
    public function name(): string
    {
        return 'claude';
    }

    public function generateReply(ReplyGenerationRequest $request): ReplyGenerationResult
    {
        throw new ProviderNotImplementedException($this->name());
    }
}
