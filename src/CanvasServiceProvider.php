<?php

namespace CanvasLMS\Laravel;

use CanvasLMS\Config;
use CanvasLMS\Laravel\Commands\TestConnectionCommand;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class CanvasServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Merge package configuration with application configuration
        $this->mergeConfigFrom(
            __DIR__ . '/../config/canvas.php',
            'canvas'
        );

        // Register the CanvasManager as a singleton
        $this->app->singleton(CanvasManager::class, function ($app) {
            return new CanvasManager($app['config']['canvas']);
        });

        // Register the CanvasManager as 'canvas' for facade access
        $this->app->singleton('canvas', function ($app) {
            return $app->make(CanvasManager::class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure Canvas LMS Kit with Laravel settings
        $this->configureCanvas();

        // Register publishable resources
        if ($this->app->runningInConsole()) {
            // Publish configuration file
            $this->publishes([
                __DIR__ . '/../config/canvas.php' => config_path('canvas.php'),
            ], 'canvas-config');

            // Register Artisan commands
            $this->commands([
                TestConnectionCommand::class,
            ]);
        }
    }

    /**
     * Configure the Canvas LMS Kit with Laravel configuration.
     */
    protected function configureCanvas(): void
    {
        $defaultConnection = config('canvas.default', 'main');
        $config = config("canvas.connections.{$defaultConnection}");

        if ($config === null) {
            return;
        }

        // Set API credentials
        if (isset($config['api_key']) && $config['api_key'] !== '') {
            Config::setApiKey($config['api_key']);
        }

        if (isset($config['base_url']) && $config['base_url'] !== '') {
            Config::setBaseUrl($config['base_url']);
        }

        // Set optional configuration
        if (isset($config['account_id'])) {
            Config::setAccountId($config['account_id']);
        }

        if (isset($config['timeout'])) {
            Config::setTimeout($config['timeout']);
        }

        // Configure logging
        if (isset($config['log_channel']) && $config['log_channel'] !== '') {
            try {
                $logger = Log::channel($config['log_channel']);
                Config::setLogger($logger);
            } catch (\Exception $e) {
                // Silently fail if log channel doesn't exist
                // This prevents breaking the application during config caching
            }
        }

        // Set API version if configured
        if (isset($config['api_version']) && $config['api_version'] !== '') {
            Config::setApiVersion($config['api_version']);
        }

        // Configure middleware if specified
        if (isset($config['middleware']) && is_array($config['middleware'])) {
            Config::setMiddleware($config['middleware']);
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return [
            CanvasManager::class,
            'canvas',
        ];
    }
}
