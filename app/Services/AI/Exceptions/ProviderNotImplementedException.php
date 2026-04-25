<?php

namespace App\Services\AI\Exceptions;

class ProviderNotImplementedException extends AIException
{
    public function __construct(string $provider)
    {
        parent::__construct(
            sprintf('The %s provider is configured as a placeholder and is not implemented yet.', $provider),
            sprintf('The %s provider is reserved for a future module. Switch AI_PROVIDER to gemini or groq for the MVP.', ucfirst($provider)),
        );
    }
}
