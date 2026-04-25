<?php

namespace App\Services\AI\Exceptions;

class UnsupportedProviderException extends AIException
{
    public function __construct(string $provider)
    {
        parent::__construct(
            sprintf('Unsupported AI provider [%s].', $provider),
            sprintf('The AI provider "%s" is not supported. Choose one of the configured providers in AI_PROVIDER.', $provider),
        );
    }
}
