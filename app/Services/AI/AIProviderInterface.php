<?php

namespace App\Services\AI;

use App\Services\AI\DTOs\ReplyGenerationRequest;
use App\Services\AI\DTOs\ReplyGenerationResult;

interface AIProviderInterface
{
    public function name(): string;

    public function generateReply(ReplyGenerationRequest $request): ReplyGenerationResult;
}
