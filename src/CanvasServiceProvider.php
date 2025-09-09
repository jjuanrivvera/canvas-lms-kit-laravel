<?php

namespace CanvasLMS\Laravel;

use CanvasLMS\Laravel\Commands\TestConnectionCommand;
use CanvasLMS\Laravel\Concerns\ConfiguresCanvas;
use CanvasLMS\Laravel\Contracts\CanvasManagerInterface;
use CanvasLMS\Laravel\Validation\ConfigurationValidator;
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

        // Bind the interface to the same singleton implementation
        $this->app->bind(CanvasManagerInterface::class, function ($app) {
            return $app->make(CanvasManager::class);
        });

        // Register the CanvasManager as 'canvas' for facade access
        $this->app->singleton('canvas', function ($app) {
            return $app->make(CanvasManager::class);
        });

        // Defer validation until after configuration is fully loaded
        // This prevents issues with config caching and improves boot performance
        $this->app->booting(function () {
            $this->validateConfiguration();
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
     * Validate Canvas configuration with performance optimizations.
     *
     * This method implements several performance optimizations:
     * - Skips validation when config is cached in production
     * - Only validates in specific environments
     * - Uses validation result caching
     */
    protected function validateConfiguration(): void
    {
        // Skip validation when config is cached in production for better performance
        // Use method_exists to safely check for configurationIsCached method
        $isConfigCached = method_exists($this->app, 'configurationIsCached')
            && $this->app->configurationIsCached();
        $isProduction = (bool) $this->app->environment('production');

        if ($isConfigCached && $isProduction) {
            return;
        }

        $validationEnabled = config('canvas.validation.enabled', true);

        if ($validationEnabled !== true) {
            return;
        }

        // Only validate in specified environments (defaults: local, testing, staging)
        $validationEnvironments = config('canvas.validation.environments', ['local', 'testing', 'staging']);
        if (! (bool) $this->app->environment($validationEnvironments)) {
            return;
        }

        $canvasConfig = config('canvas');

        if ($canvasConfig === null) {
            throw new \InvalidArgumentException(
                'Canvas configuration is not available. Please ensure the canvas.php config file is properly configured.'
            );
        }

        try {
            // Use optimized validation with caching
            $useCache = config('canvas.validation.cache_results', true);
            ConfigurationValidator::validateCanvasConfigurationOptimized($canvasConfig, $useCache);
        } catch (\InvalidArgumentException $e) {
            // Re-throw with additional context about how to fix the issue
            throw new \InvalidArgumentException(
                "Canvas configuration validation failed: {$e->getMessage()}\n\n" .
                'To disable configuration validation, set CANVAS_VALIDATION_ENABLED=false in your .env file. ' .
                'However, fixing the configuration issues is recommended for security and reliability.'
            );
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
            CanvasManagerInterface::class,
            'canvas',
        ];
    }
}
