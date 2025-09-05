<?php

use CanvasLMS\Config;
use CanvasLMS\Laravel\CanvasManager;

beforeEach(function () {
    $this->config = [
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

    $this->manager = new CanvasManager($this->config);
});

test('initializes with default connection', function () {
    expect($this->manager->getConnection())->toBe('main');
});

test('can switch connections', function () {
    $this->manager->connection('secondary');

    expect($this->manager->getConnection())->toBe('secondary');
    expect(Config::getApiKey())->toBe('secondary-api-key');
});

test('throws exception for invalid connection', function () {
    $this->manager->connection('nonexistent');
})->throws(InvalidArgumentException::class, 'Canvas connection [nonexistent] is not configured');

test('returns available connections', function () {
    $connections = $this->manager->getAvailableConnections();

    expect($connections)->toBe(['main', 'secondary']);
});

test('executes callback using specific connection', function () {
    $result = $this->manager->usingConnection('secondary', function ($manager) {
        return $manager->getConnection();
    });

    expect($result)->toBe('secondary');
    expect($this->manager->getConnection())->toBe('main'); // Restored
});

test('provides dynamic access to canvas api classes', function () {
    expect($this->manager->courses())->toBe(\CanvasLMS\Api\Courses\Course::class);
    expect($this->manager->users())->toBe(\CanvasLMS\Api\Users\User::class);
    expect($this->manager->enrollments())->toBe(\CanvasLMS\Api\Enrollments\Enrollment::class);
});

test('throws exception for invalid api method', function () {
    $this->manager->nonexistentApi();
})->throws(BadMethodCallException::class);
