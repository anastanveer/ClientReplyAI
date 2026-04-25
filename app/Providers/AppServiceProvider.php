<?php

namespace App\Providers;

use App\Services\AI\AIService;
use App\Services\Prompts\PromptContextResolver;
use App\Services\Prompts\ReplyPromptBuilder;
use App\Services\Replies\ReplyGenerationService;
use App\Services\Usage\UsageLimitService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(AIService::class, function (): AIService {
            return new AIService(config('ai'));
        });

        $this->app->singleton(PromptContextResolver::class);
        $this->app->singleton(ReplyPromptBuilder::class);
        $this->app->singleton(UsageLimitService::class);
        $this->app->singleton(ReplyGenerationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
