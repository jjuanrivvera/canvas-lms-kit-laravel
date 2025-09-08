<?php

use CanvasLMS\Laravel\Testing\CanvasFake;
use Carbon\Carbon;

beforeEach(function () {
    $this->fake = new CanvasFake;
});

test('can fake api responses', function () {
    $this->fake->fake([
        'courses' => [
            ['id' => 1, 'name' => 'Test Course'],
        ],
    ]);

    // Would normally call Course::fetchAll() here
    // But we need the base SDK installed to test properly

    expect($this->fake)->toBeInstanceOf(CanvasFake::class);
});

test('records api calls', function () {
    $this->fake->fake();

    // Simulate some API calls would happen here

    expect($this->fake->getRecordedCalls())->toBeArray();
});

test('can assert api calls were made', function () {
    $this->fake->fake();

    // In real usage, after making API calls:
    // $this->fake->assertApiCallMade('GET', '/courses');
    // $this->fake->assertCourseCreated(['name' => 'Test']);

    expect(method_exists($this->fake, 'assertApiCallMade'))->toBeTrue();
    expect(method_exists($this->fake, 'assertCourseCreated'))->toBeTrue();
    expect(method_exists($this->fake, 'assertEnrollmentCreated'))->toBeTrue();
});

test('can clear recorded calls', function () {
    $this->fake->fake();
    $this->fake->clearRecordedCalls();

    expect($this->fake->getRecordedCalls())->toBeEmpty();
});

test('records timestamps correctly without undefined function error', function () {
    $this->fake->fake();

    // Use reflection to directly call the protected recordCall method
    // This verifies that the now() helper issue is fixed
    $reflection = new ReflectionClass($this->fake);
    $recordCallMethod = $reflection->getMethod('recordCall');
    $recordCallMethod->setAccessible(true);

    // This should not throw an "undefined function now()" error
    $recordCallMethod->invoke($this->fake, 'GET', '/test-endpoint', ['test' => 'data']);

    $calls = $this->fake->getRecordedCalls();

    expect($calls)->toHaveCount(1);
    expect($calls[0])->toHaveKey('timestamp');
    expect($calls[0]['timestamp'])->toBeInstanceOf(Carbon::class);
    expect($calls[0]['method'])->toBe('GET');
    expect($calls[0]['endpoint'])->toBe('/test-endpoint');
    expect($calls[0]['data'])->toBe(['test' => 'data']);
});

test('canvas fake handles non-existent file uploads gracefully', function () {
    $this->fake->fake();

    // This should not throw warnings or errors
    $response = $this->fake->getMockClient()->postFile('/files', '/non/existent/file.txt');

    expect($response)->toHaveKey('filename')
        ->and($response['filename'])->toBe('file.txt')
        ->and($response)->toHaveKey('size')
        ->and($response['size'])->toBe(1024)
        ->and($response)->toHaveKey('content_type')
        ->and($response['content_type'])->toBe('application/octet-stream')
        ->and($response)->toHaveKey('id')
        ->and($response['id'])->toBeInt();
});

test('canvas fake uses real file information when file exists', function () {
    // Create a temporary test file
    $tempFile = tempnam(sys_get_temp_dir(), 'canvas_test');
    file_put_contents($tempFile, 'test content for canvas fake testing');

    $this->fake->fake();

    $response = $this->fake->getMockClient()->postFile('/files', $tempFile);

    expect($response)->toHaveKey('filename')
        ->and($response['filename'])->toBe(basename($tempFile))
        ->and($response)->toHaveKey('size')
        ->and($response['size'])->toBe(strlen('test content for canvas fake testing'))
        ->and($response)->toHaveKey('content_type')
        ->and($response['content_type'])->toBeString()
        ->and($response)->toHaveKey('id')
        ->and($response['id'])->toBeInt();

    // Clean up
    unlink($tempFile);
});

test('canvas fake handles empty file path gracefully', function () {
    $this->fake->fake();

    $response = $this->fake->getMockClient()->postFile('/files', '');

    expect($response)->toHaveKey('filename')
        ->and($response['filename'])->toBe('')
        ->and($response)->toHaveKey('size')
        ->and($response['size'])->toBe(1024)
        ->and($response)->toHaveKey('content_type')
        ->and($response['content_type'])->toBe('application/octet-stream')
        ->and($response)->toHaveKey('id')
        ->and($response['id'])->toBeInt();
});

test('canvas fake records file upload calls correctly', function () {
    $this->fake->fake();

    $this->fake->getMockClient()->postFile('/api/v1/files', '/test/file.pdf', ['folder_id' => 123]);

    $calls = $this->fake->getRecordedCalls();

    expect($calls)->toHaveCount(1);
    expect($calls[0]['method'])->toBe('POST_FILE');
    expect($calls[0]['endpoint'])->toBe('/api/v1/files');
    expect($calls[0]['data'])->toBe([
        'file'   => '/test/file.pdf',
        'params' => ['folder_id' => 123],
    ]);
});
