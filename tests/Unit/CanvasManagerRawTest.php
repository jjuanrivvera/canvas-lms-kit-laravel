<?php

use CanvasLMS\Canvas;
use CanvasLMS\Laravel\CanvasManager;

beforeEach(function () {
    $this->config = [
        'default'     => 'main',
        'connections' => [
            'main' => [
                'api_key'    => 'test-api-key',
                'base_url'   => 'https://test.canvas.com',
                'account_id' => 1,
            ],
        ],
    ];

    $this->manager = new CanvasManager($this->config);
});

test('raw method returns Canvas SDK instance', function () {
    $raw = $this->manager->raw();

    expect($raw)
        ->toBeInstanceOf(Canvas::class);
});

test('raw method returns fresh instance each time', function () {
    $raw1 = $this->manager->raw();
    $raw2 = $this->manager->raw();

    expect($raw1)
        ->toBeInstanceOf(Canvas::class)
        ->and($raw2)
        ->toBeInstanceOf(Canvas::class);

    // Each call should return a fresh instance
    expect(spl_object_id($raw1))->not->toBe(spl_object_id($raw2));
});

test('raw method is available through facade', function () {
    // Test that the method exists and returns correct type
    $methods = get_class_methods($this->manager);

    expect($methods)
        ->toContain('raw');
});

test('raw method provides access to Canvas API methods', function () {
    $raw = $this->manager->raw();

    // Verify that the Canvas instance has the expected static methods
    $canvasClass = new ReflectionClass(Canvas::class);
    $staticMethods = array_filter(
        $canvasClass->getMethods(ReflectionMethod::IS_STATIC),
        fn ($method) => $method->isPublic()
    );

    $methodNames = array_map(fn ($method) => $method->getName(), $staticMethods);

    // Canvas facade should have these HTTP methods
    expect($methodNames)
        ->toContain('get')
        ->toContain('post');
});

test('raw usage follows expected pattern', function () {
    $raw = $this->manager->raw();

    // These would be the actual usage patterns:
    // $raw::get('/api/v1/courses')
    // $raw::post('/api/v1/courses', ['name' => 'Test'])

    // We can't test actual HTTP calls without mocking,
    // but we can verify the class structure supports it
    expect(method_exists(Canvas::class, 'get'))->toBeTrue();
    expect(method_exists(Canvas::class, 'post'))->toBeTrue();
    expect(method_exists(Canvas::class, 'put'))->toBeTrue();
    expect(method_exists(Canvas::class, 'delete'))->toBeTrue();
});

test('raw instance is independent of connection switching', function () {
    // Create a config with multiple connections
    $config = [
        'default'     => 'main',
        'connections' => [
            'main' => [
                'api_key'    => 'test-api-key',
                'base_url'   => 'https://test.canvas.com',
                'account_id' => 1,
            ],
            'test' => [
                'api_key'    => 'test-api-key-2',
                'base_url'   => 'https://test2.canvas.com',
                'account_id' => 2,
            ],
        ],
    ];

    $manager = new CanvasManager($config);

    // Get raw before switching
    $raw1 = $manager->raw();

    // Switch connection
    $manager->connection('test');

    // Get raw after switching
    $raw2 = $manager->raw();

    // Both should be Canvas instances
    expect($raw1)
        ->toBeInstanceOf(Canvas::class)
        ->and($raw2)
        ->toBeInstanceOf(Canvas::class);

    // They should be different instances
    expect(spl_object_id($raw1))->not->toBe(spl_object_id($raw2));
});

test('raw method performance is acceptable', function () {
    $iterations = 100;
    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        $this->manager->raw();
    }

    $duration = microtime(true) - $start;

    // Creating 100 Canvas instances should be fast
    expect($duration)->toBeLessThan(0.1); // Less than 100ms

    $avgTimePerCall = ($duration / $iterations) * 1000; // milliseconds
    expect($avgTimePerCall)->toBeLessThan(1); // Less than 1ms per call
});
