<?php

namespace App\Services\Prompts\DTOs;

final readonly class ResolvedPromptContext
{
    /**
     * @param  list<string>  $requestedVariants
     * @param  list<string>  $allowedVariants
     * @param  list<string>  $riskFlags
     * @param  array<string, string>  $toneRule
     * @param  array<string, string>  $useCaseRule
     * @param  array<string, string>  $languageRule
     */
    public function __construct(
        public string $message,
        public ?string $additionalContext,
        public ?string $goal,
        public ?string $receiver,
        public ?string $platform,
        public string $tone,
        public string $useCase,
        public string $language,
        public array $requestedVariants,
        public array $allowedVariants,
        public array $riskFlags,
        public array $toneRule,
        public array $useCaseRule,
        public array $languageRule,
    ) {}

    public function hasOptionalVariants(): bool
    {
        return $this->allowedVariants !== [];
    }

    public function shouldIncludeRiskNote(): bool
    {
        return $this->riskFlags !== [];
    }
}
