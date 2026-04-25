<?php

namespace App\Services\AI\DTOs;

final readonly class ReplyGenerationRequest
{
    /**
     * @param  list<array{role:string, content:string}>  $conversation
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public string $prompt,
        public ?string $systemPrompt = null,
        public array $conversation = [],
        public ?string $provider = null,
        public ?string $model = null,
        public ?float $temperature = null,
        public ?int $maxOutputTokens = null,
        public array $metadata = [],
    ) {}

    /**
     * @return list<array{role:string, content:string}>
     */
    public function messages(): array
    {
        $messages = [];

        foreach ($this->conversation as $message) {
            $role = strtolower((string) ($message['role'] ?? ''));
            $content = trim((string) ($message['content'] ?? ''));

            if ($content === '' || ! in_array($role, ['user', 'assistant'], true)) {
                continue;
            }

            $messages[] = [
                'role' => $role,
                'content' => $content,
            ];
        }

        $prompt = trim($this->prompt);

        if ($prompt !== '') {
            $messages[] = [
                'role' => 'user',
                'content' => $prompt,
            ];
        }

        return $messages;
    }
}
