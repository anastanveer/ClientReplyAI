<?php

return [
    'default_provider' => env('AI_PROVIDER', 'gemini'),
    'fallback_provider' => env('AI_FALLBACK_PROVIDER', 'groq'),

    'timeout' => (int) env('AI_TIMEOUT', 20),

    'provider_access' => [
        'openai' => ['premium', 'testing'],
    ],

    'providers' => [
        'gemini' => [
            'api_key' => env('GEMINI_API_KEY'),
            'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
            'endpoint' => env('GEMINI_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta/models'),
        ],

        'groq' => [
            'api_key' => env('GROQ_API_KEY'),
            'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
            'endpoint' => env('GROQ_ENDPOINT', 'https://api.groq.com/openai/v1/chat/completions'),
        ],

        'claude' => [
            'api_key' => env('CLAUDE_API_KEY'),
            'model' => env('CLAUDE_MODEL', 'claude-placeholder'),
            'endpoint' => env('CLAUDE_ENDPOINT', 'https://api.anthropic.com/v1/messages'),
        ],

        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'openai-placeholder'),
            'endpoint' => env('OPENAI_ENDPOINT', 'https://api.openai.com/v1/chat/completions'),
        ],

        'ollama' => [
            'base_url' => env('OLLAMA_URL', 'http://127.0.0.1:11434'),
            'model' => env('OLLAMA_MODEL', 'llama3.2'),
        ],
    ],
];
