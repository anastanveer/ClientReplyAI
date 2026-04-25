<?php

namespace App\Services\AI\Exceptions;

class MissingApiKeyException extends AIException
{
    public function __construct(string $provider, string $environmentVariable)
    {
        parent::__construct(
            sprintf('The %s provider is missing its API key. Expected environment variable: %s.', $provider, $environmentVariable),
            sprintf('The %s provider is not configured yet. Add %s to your .env file and try again.', ucfirst($provider), $environmentVariable),
        );
    }
}
