<?php

namespace App\Services\AIEngine;

final readonly class SmartModeHints
{
    public function __construct(
        public string $platformStyle,
        public string $intensitySignal,
        public string $suggestedLength,
        public ?string $contextHint,
    ) {}
}
