<?php

namespace App\Services\Replies\Exceptions;

use RuntimeException;

class InvalidAiResponseException extends RuntimeException
{
    public function __construct(
        protected string $userMessage = 'The AI reply could not be processed cleanly. Please try again.',
        string $message = 'Invalid AI response format.',
    ) {
        parent::__construct($message);
    }

    public function userMessage(): string
    {
        return $this->userMessage;
    }
}
