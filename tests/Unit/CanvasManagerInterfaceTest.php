<?php

use CanvasLMS\Laravel\CanvasManager;
use CanvasLMS\Laravel\Contracts\CanvasManagerInterface;

test('CanvasManager implements CanvasManagerInterface', function () {
    $config = [
        'default'     => 'main',
        'connections' => [
            'main' => [
                'api_key'    => 'test-api-key',
                'base_url'   => 'https://test.canvas.com',
                'account_id' => 1,
            ],
        ],
    ];

    $manager = new CanvasManager($config);

    expect($manager)->toBeInstanceOf(CanvasManagerInterface::class);
});

test('interface defines expected contract methods', function () {
    $reflection = new ReflectionClass(CanvasManagerInterface::class);
    $methods = $reflection->getMethods();
    $methodNames = array_map(fn ($method) => $method->getName(), $methods);

    expect($methodNames)->toContain('connection')
        ->and($methodNames)->toContain('getConnection')
        ->and($methodNames)->toContain('getConnectionConfig')
        ->and($methodNames)->toContain('getAvailableConnections')
        ->and($methodNames)->toContain('usingConnection')
        ->and($methods)->toHaveCount(5);
});

test('can mock CanvasManagerInterface for testing', function () {
    $mock = mock(CanvasManagerInterface::class);

    $mock->shouldReceive('connection')
        ->with('sandbox')
        ->once()
        ->andReturnSelf();

    $mock->shouldReceive('getConnection')
        ->once()
        ->andReturn('sandbox');

    $result = $mock->connection('sandbox');
    expect($result)->toBe($mock);
    expect($mock->getConnection())->toBe('sandbox');
});

test('interface contract matches CanvasManager implementation', function () {
    $config = [
        'default'     => 'main',
        'connections' => [
            'main' => [
                'api_key'    => 'main-api-key',
                'base_url'   => 'https://main.canvas.com',
                'account_id' => 1,
            ],
            'secondary' => [
                'api_key'    => 'secondary-api-key',
                'base_url'   => 'https://secondary.canvas.com',
                'account_id' => 2,
            ],
        ],
    ];

    $manager = new CanvasManager($config);

    // Test interface methods work as expected
    expect($manager->getConnection())->toBe('main');
    expect($manager->getAvailableConnections())->toBe(['main', 'secondary']);
    expect($manager->getConnectionConfig())->toBe($config['connections']['main']);

    $manager->connection('secondary');
    expect($manager->getConnection())->toBe('secondary');

    $result = $manager->usingConnection('main', fn ($m) => $m->getConnection());
    expect($result)->toBe('main');
    expect($manager->getConnection())->toBe('secondary'); // Should be restored
});

test('dependency injection works with interface', function () {
    $config = [
        'default'     => 'main',
        'connections' => [
            'main' => ['api_key' => 'test', 'base_url' => 'https://test.com', 'account_id' => 1],
            'test' => ['api_key' => 'test2', 'base_url' => 'https://test2.com', 'account_id' => 2],
        ],
    ];

    $manager = new CanvasManager($config);

    // This test demonstrates how the interface can be used for dependency injection
    $testService = new class($manager)
    {
        public function __construct(private CanvasManagerInterface $canvas) {}

        public function getCurrentConnection(): string
        {
            return $this->canvas->getConnection();
        }

        public function switchToConnection(string $name): void
        {
            $this->canvas->connection($name);
        }
    };

    expect($testService->getCurrentConnection())->toBe('main');

    $testService->switchToConnection('test');
    expect($testService->getCurrentConnection())->toBe('test');
});
