<?php

use CanvasLMS\Config;
use CanvasLMS\Laravel\CanvasManager;
use CanvasLMS\Laravel\Contracts\CanvasManagerInterface;
use CanvasLMS\Laravel\Facades\Canvas;
use Illuminate\Support\Facades\Artisan;

test('service provider registers canvas manager', function () {
    $manager = app(CanvasManager::class);

    expect($manager)->toBeInstanceOf(CanvasManager::class);
    expect(app('canvas'))->toBe($manager);
});

test('service provider auto configures from laravel config', function () {
    // Config is set in TestCase defineEnvironment
    expect(Config::getApiKey())->toBe('test-api-key');
    expect(Config::getBaseUrl())->toContain('canvas.test.com');
    expect(Config::getAccountId())->toBe(1);
});

test('publishes configuration file', function () {
    $configPath = config_path('canvas.php');

    // Clean up if exists
    if (file_exists($configPath)) {
        unlink($configPath);
    }

    Artisan::call('vendor:publish', [
        '--tag'   => 'canvas-config',
        '--force' => true,
    ]);

    expect($configPath)->toBeFile();

    // Clean up
    if (file_exists($configPath)) {
        unlink($configPath);
    }
});

test('canvas facade works correctly', function () {
    expect(Canvas::getConnection())->toBe('testing');
    expect(Canvas::getAvailableConnections())->toContain('testing', 'secondary');
});

test('service provider binds interface to implementation', function () {
    $manager = app(CanvasManagerInterface::class);

    expect($manager)->toBeInstanceOf(CanvasManager::class);
    expect($manager)->toBeInstanceOf(CanvasManagerInterface::class);
});

test('interface and concrete class resolve to same singleton instance', function () {
    $concreteManager = app(CanvasManager::class);
    $interfaceManager = app(CanvasManagerInterface::class);

    // Both should be instances of CanvasManager
    expect($concreteManager)->toBeInstanceOf(CanvasManager::class);
    expect($interfaceManager)->toBeInstanceOf(CanvasManager::class);

    // Interface should resolve to the same singleton instance
    expect($interfaceManager)->toBe($concreteManager)
        ->and($interfaceManager->getConnection())->toBe($concreteManager->getConnection());
});

test('artisan command is registered', function () {
    $commands = Artisan::all();

    expect($commands)->toHaveKey('canvas:test');
});
