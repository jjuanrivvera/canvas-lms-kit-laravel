<?php

namespace CanvasLMS\Laravel\Tests;

use CanvasLMS\Laravel\CanvasServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array<string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            CanvasServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function defineEnvironment($app): void
    {
        // Set up default Canvas configuration for testing
        $app['config']->set('canvas.default', 'testing');

        $app['config']->set('canvas.connections.testing', [
            'api_key'     => 'test-api-key',
            'base_url'    => 'https://canvas.test.com',
            'account_id'  => 1,
            'timeout'     => 30,
            'log_channel' => 'stack',
        ]);

        $app['config']->set('canvas.connections.secondary', [
            'api_key'    => 'secondary-api-key',
            'base_url'   => 'https://secondary.test.com',
            'account_id' => 2,
        ]);

        $app['config']->set('canvas.testing.fake', true);
    }

    /**
     * Get package aliases.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array<string, string>
     */
    protected function getPackageAliases($app): array
    {
        return [
            'Canvas' => \CanvasLMS\Laravel\Facades\Canvas::class,
        ];
    }
}
