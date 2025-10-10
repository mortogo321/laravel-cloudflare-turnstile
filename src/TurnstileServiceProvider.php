<?php

namespace Mortogo321\LaravelCloudflareTurnstile;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Mortogo321\LaravelCloudflareTurnstile\Services\TurnstileService;
use Mortogo321\LaravelCloudflareTurnstile\View\Components\TurnstileWidget;

class TurnstileServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/turnstile.php',
            'turnstile'
        );

        $this->app->singleton(TurnstileService::class, function ($app) {
            return new TurnstileService(
                config('turnstile.secret_key'),
                config('turnstile.verify_url')
            );
        });

        $this->app->alias(TurnstileService::class, 'turnstile');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config
        $this->publishes([
            __DIR__.'/../config/turnstile.php' => config_path('turnstile.php'),
        ], 'turnstile-config');

        // Publish assets
        $this->publishes([
            __DIR__.'/../resources/js' => public_path('vendor/turnstile/js'),
        ], 'turnstile-assets');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'turnstile');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/turnstile'),
        ], 'turnstile-views');

        // Register Blade component
        Blade::component('turnstile', TurnstileWidget::class);

        // Register validation rule
        Validator::extend('turnstile', function ($attribute, $value, $parameters, $validator) {
            if (empty($value)) {
                return false;
            }

            $turnstile = app(TurnstileService::class);
            $result = $turnstile->verify($value, request()->ip());

            return $result['success'] ?? false;
        }, 'The Turnstile verification failed. Please try again.');
    }
}
