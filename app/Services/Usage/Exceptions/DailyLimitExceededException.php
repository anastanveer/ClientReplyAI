<?php

namespace App\Services\Usage\Exceptions;

use RuntimeException;

class DailyLimitExceededException extends RuntimeException
{
    public function __construct(
        protected string $userMessage,
    ) {
        parent::__construct($userMessage);
    }

    public function userMessage(): string
    {
        return $this->userMessage;
    }
}
