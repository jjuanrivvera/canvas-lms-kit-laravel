<?php

use CanvasLMS\Laravel\Testing\CanvasFake;
use CanvasLMS\Laravel\Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(TestCase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function canvas(): CanvasFake
{
    return new CanvasFake;
}

function fakeCanvasResponse(string $endpoint, mixed $response): void
{
    $fake = new CanvasFake;
    $fake->fake([$endpoint => $response]);
}

function assertCanvasConnectionConfigured(): void
{
    expect(config('canvas.connections.testing.api_key'))->not->toBeNull();
    expect(config('canvas.connections.testing.base_url'))->not->toBeNull();
}
