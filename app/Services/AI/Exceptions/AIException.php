<?php

namespace App\Services\AI\Exceptions;

use RuntimeException;

class AIException extends RuntimeException
{
    public function __construct(
        string $message,
        protected string $userMessage,
    ) {
        parent::__construct($message);
    }

    public function userMessage(): string
    {
        return $this->userMessage;
    }
}
