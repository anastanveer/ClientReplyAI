<?php

namespace App\Services\AI\Exceptions;

class ProvidersExhaustedException extends AIException
{
    /**
     * @param  list<string>  $providersTried
     * @param  list<string>  $reasons
     */
    public function __construct(array $providersTried, array $reasons)
    {
        $providerList = implode(', ', $providersTried);
        $reasonList = implode(' | ', $reasons);

        parent::__construct(
            sprintf('All configured providers failed. Tried: %s. Reasons: %s', $providerList, $reasonList),
            'The AI providers are unavailable or not configured correctly right now. Please check your .env keys and try again.',
        );
    }
}
