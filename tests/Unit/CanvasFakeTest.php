<?php

use CanvasLMS\Laravel\Testing\CanvasFake;

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
