<?php

namespace App\Services\Replies\DTOs;

use App\Models\Chat;
use App\Models\ChatMessage;

final readonly class GeneratedReplyData
{
    /**
     * @param  array<string, string>  $variants
     * @param  array<string, int|float|null>  $usage
     */
    public function __construct(
        public Chat $chat,
        public ChatMessage $userMessage,
        public ChatMessage $assistantMessage,
        public string $bestReply,
        public ?string $riskNote,
        public ?string $coachNote,
        public ?string $nextStep,
        public array $variants,
        public string $provider,
        public string $model,
        public ?string $finishReason,
        public array $usage,
        public bool $usedFallback,
    ) {}
}
