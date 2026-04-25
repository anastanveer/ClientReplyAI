<?php

namespace App\Services\Prompts\DTOs;

final readonly class BuiltPrompt
{
    /**
     * @param  list<string>  $variants
     * @param  list<string>  $riskFlags
     * @param  array<string, mixed>  $responseSchema
     */
    public function __construct(
        public string $systemPrompt,
        public string $userPrompt,
        public array $variants,
        public array $riskFlags,
        public array $responseSchema,
    ) {}
}
