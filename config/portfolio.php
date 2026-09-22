<?php

return [
    'ai' => [
        'provider' => env('AI_PROVIDER', 'openai-compatible'),
        'base_url' => env('AI_BASE_URL', 'https://api.openai.com/v1'),
        'key' => env('AI_API_KEY'),
        'model' => env('AI_MODEL'),
        'daily_limit' => (int) env('AI_DAILY_LIMIT', 100),
        'retention_days' => (int) env('AI_RETENTION_DAYS', 0),
    ],
];
