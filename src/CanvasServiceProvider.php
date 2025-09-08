<?php

namespace CanvasLMS\Laravel;

use CanvasLMS\Laravel\Commands\TestConnectionCommand;
use CanvasLMS\Laravel\Concerns\ConfiguresCanvas;
use Illuminate\Support\ServiceProvider;

class CanvasServiceProvider extends ServiceProvider
{
    use ConfiguresCanvas;

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

        if ($config !== null) {
            $this->applyCanvasConfiguration($config);
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
