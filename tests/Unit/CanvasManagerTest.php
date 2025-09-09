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

test('api class map contains all expected canvas apis', function () {
    $reflection = new ReflectionClass(CanvasManager::class);
    $classMap = $reflection->getConstant('API_CLASS_MAP');

    // Test that the constant exists and is an array
    expect($classMap)->toBeArray();
    expect($classMap)->not->toBeEmpty();

    // Test core resources exist
    expect($classMap)->toHaveKey('courses');
    expect($classMap)->toHaveKey('course');
    expect($classMap)->toHaveKey('users');
    expect($classMap)->toHaveKey('user');
    expect($classMap)->toHaveKey('accounts');
    expect($classMap)->toHaveKey('account');

    // Test that values are valid class name strings
    expect($classMap['courses'])->toBe(\CanvasLMS\Api\Courses\Course::class);
    expect($classMap['course'])->toBe(\CanvasLMS\Api\Courses\Course::class);
    expect($classMap['users'])->toBe(\CanvasLMS\Api\Users\User::class);
    expect($classMap['user'])->toBe(\CanvasLMS\Api\Users\User::class);

    // Test both camelCase and snake_case variants exist for key methods
    expect($classMap)->toHaveKey('discussionTopics');
    expect($classMap)->toHaveKey('discussion_topics');
    expect($classMap['discussionTopics'])->toBe($classMap['discussion_topics']);

    expect($classMap)->toHaveKey('mediaObjects');
    expect($classMap)->toHaveKey('media_objects');
    expect($classMap['mediaObjects'])->toBe($classMap['media_objects']);

    // Test that all values are properly formatted class strings
    foreach ($classMap as $method => $className) {
        expect($className)->toBeString("Method {$method} should map to a string");
        expect($className)->toStartWith('CanvasLMS\\Api\\', "Class {$className} should be in CanvasLMS\\Api namespace");
    }

    // Test that known existing classes are properly mapped
    $knownClasses = [
        'courses'     => \CanvasLMS\Api\Courses\Course::class,
        'users'       => \CanvasLMS\Api\Users\User::class,
        'enrollments' => \CanvasLMS\Api\Enrollments\Enrollment::class,
    ];

    foreach ($knownClasses as $method => $expectedClass) {
        expect($classMap)->toHaveKey($method);
        expect($classMap[$method])->toBe($expectedClass);
        expect(class_exists($expectedClass))->toBeTrue("Known class {$expectedClass} should exist");
    }
});
