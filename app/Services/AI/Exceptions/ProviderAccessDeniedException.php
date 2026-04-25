<?php

namespace App\Services\AI\Exceptions;

class ProviderAccessDeniedException extends AIException
{
    /**
     * @param  list<string>  $allowedScopes
     */
    public function __construct(string $provider, array $allowedScopes)
    {
        $scopeList = implode(', ', $allowedScopes);

        parent::__construct(
            sprintf('The %s provider is restricted to these scopes: %s.', $provider, $scopeList),
            sprintf('The %s provider is only available for %s.', ucfirst($provider), $scopeList),
        );
    }
}
