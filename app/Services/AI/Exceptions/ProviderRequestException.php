<?php

namespace App\Services\AI\Exceptions;

class ProviderRequestException extends AIException
{
    public function __construct(string $provider, string $message)
    {
        parent::__construct(
            sprintf('%s request failed: %s', ucfirst($provider), $message),
            sprintf('The %s provider could not generate a reply right now. Please try again in a moment.', ucfirst($provider)),
        );
    }
}
