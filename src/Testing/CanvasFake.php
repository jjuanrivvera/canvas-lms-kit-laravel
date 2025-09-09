<?php

namespace CanvasLMS\Laravel\Testing;

use CanvasLMS\Api\AbstractBaseApi;
use CanvasLMS\Interfaces\HttpClientInterface;
use Carbon\Carbon;
use Mockery;
use PHPUnit\Framework\Assert;

/**
 * Canvas Fake for testing Canvas LMS integrations.
 *
 * This class provides utilities for mocking Canvas API responses
 * and asserting API interactions during testing.
 */
class CanvasFake
{
    /**
     * The fake responses to return.
     *
     * @var array<string, mixed>
     */
    protected array $responses = [];

    /**
     * The recorded API calls.
     *
     * @var array<array{method: string, endpoint: string, data: mixed}>
     */
    protected array $recorded = [];

    /**
     * The mock HTTP client.
     *
     * @var HttpClientInterface|\Mockery\MockInterface|null
     */
    protected $mockClient = null;

    /**
     * Enable fake mode with optional responses.
     *
     * @param array<string, mixed> $responses
     */
    public function fake(array $responses = []): void
    {
        $this->responses = $responses;
        $this->recorded = [];

        // Create a mock HTTP client
        $this->mockClient = Mockery::mock(HttpClientInterface::class);

        // Set up default responses
        $this->setupMockResponses();

        // Inject the mock client into the Canvas API
        AbstractBaseApi::setApiClient($this->mockClient);
    }

    /**
     * Set up mock responses based on the configured responses.
     */
    protected function setupMockResponses(): void
    {
        if (! $this->mockClient) {
            return;
        }

        // Default to returning empty arrays for list endpoints
        $this->mockClient->shouldReceive('get')
            ->andReturnUsing(function ($endpoint, $params = []) {
                $this->recordCall('GET', $endpoint, $params);

                // Check if we have a specific response for this endpoint
                foreach ($this->responses as $pattern => $response) {
                    if ($this->matchesPattern($endpoint, $pattern)) {
                        return $this->formatResponse($response);
                    }
                }

                // Default responses based on endpoint patterns
                if (str_contains($endpoint, '/courses')) {
                    return [];
                } elseif (str_contains($endpoint, '/users')) {
                    return [];
                } elseif (str_contains($endpoint, '/enrollments')) {
                    return [];
                }

                return [];
            });

        // Mock POST requests
        $this->mockClient->shouldReceive('post')
            ->andReturnUsing(function ($endpoint, $data = []) {
                $this->recordCall('POST', $endpoint, $data);

                // Check for specific responses
                foreach ($this->responses as $pattern => $response) {
                    if ($this->matchesPattern($endpoint, $pattern)) {
                        return $this->formatResponse($response);
                    }
                }

                // Return the posted data with a fake ID
                return array_merge($data, ['id' => rand(1000, 9999)]);
            });

        // Mock PUT requests
        $this->mockClient->shouldReceive('put')
            ->andReturnUsing(function ($endpoint, $data = []) {
                $this->recordCall('PUT', $endpoint, $data);

                // Check for specific responses
                foreach ($this->responses as $pattern => $response) {
                    if ($this->matchesPattern($endpoint, $pattern)) {
                        return $this->formatResponse($response);
                    }
                }

                return $data;
            });

        // Mock DELETE requests
        $this->mockClient->shouldReceive('delete')
            ->andReturnUsing(function ($endpoint) {
                $this->recordCall('DELETE', $endpoint, null);

                // Check for specific responses
                foreach ($this->responses as $pattern => $response) {
                    if ($this->matchesPattern($endpoint, $pattern)) {
                        return $this->formatResponse($response);
                    }
                }

                return ['success' => true];
            });

        // Mock file uploads
        $this->mockClient->shouldReceive('postFile')
            ->andReturnUsing(function ($endpoint, $filePath, $params = []) {
                $this->recordCall('POST_FILE', $endpoint, ['file' => $filePath, 'params' => $params]);

                // Check if file exists and is readable to prevent warnings
                $fileExists = file_exists($filePath) && is_readable($filePath);

                return [
                    'id'           => rand(1000, 9999),
                    'filename'     => basename($filePath),
                    'size'         => $fileExists ? (filesize($filePath) ?: 0) : 1024,
                    'content_type' => $fileExists ? (mime_content_type($filePath) ?: 'application/octet-stream') : 'application/octet-stream',
                ];
            });
    }

    /**
     * Check if an endpoint matches a pattern.
     */
    protected function matchesPattern(string $endpoint, string $pattern): bool
    {
        // Allow wildcards in patterns
        $pattern = str_replace('*', '.*', $pattern);
        $pattern = str_replace('/', '\/', $pattern);

        return (bool) preg_match('/^' . $pattern . '$/', $endpoint);
    }

    /**
     * Format a response for the mock.
     */
    protected function formatResponse(mixed $response): mixed
    {
        // If response is a callable, execute it
        if (is_callable($response)) {
            return $response();
        }

        return $response;
    }

    /**
     * Record an API call.
     */
    protected function recordCall(string $method, string $endpoint, mixed $data): void
    {
        $this->recorded[] = [
            'method'    => $method,
            'endpoint'  => $endpoint,
            'data'      => $data,
            'timestamp' => Carbon::now(),
        ];
    }

    /**
     * Assert that a course was created with the given data.
     *
     * @param array<string, mixed> $expectedData
     */
    public function assertCourseCreated(array $expectedData = []): void
    {
        $this->assertApiCallMade('POST', '/courses', $expectedData);
    }

    /**
     * Assert that an enrollment was created.
     */
    public function assertEnrollmentCreated(int $userId, int $courseId, ?string $type = null): void
    {
        $found = false;

        foreach ($this->recorded as $call) {
            if ($call['method'] === 'POST' &&
                str_contains($call['endpoint'], '/enrollments')) {

                $data = $call['data'] ?? [];

                if (isset($data['enrollment'])) {
                    $enrollment = $data['enrollment'];

                    $matches = $enrollment['user_id'] == $userId &&
                              $enrollment['course_id'] == $courseId;

                    if ($type !== null) {
                        $matches = $matches && $enrollment['type'] == $type;
                    }

                    if ($matches) {
                        $found = true;
                        break;
                    }
                }
            }
        }

        Assert::assertTrue(
            $found,
            "Failed asserting that enrollment was created for user {$userId} in course {$courseId}"
        );
    }

    /**
     * Assert that an API call was made.
     *
     * @param array<string, mixed> $expectedData
     */
    public function assertApiCallMade(string $method, string $endpointPattern, array $expectedData = []): void
    {
        $found = false;

        foreach ($this->recorded as $call) {
            if ($call['method'] === $method &&
                $this->matchesPattern($call['endpoint'], $endpointPattern)) {

                if (empty($expectedData)) {
                    $found = true;
                    break;
                }

                // Check if expected data matches
                $actualData = $call['data'] ?? [];
                $matches = true;

                foreach ($expectedData as $key => $value) {
                    if (! isset($actualData[$key]) || $actualData[$key] != $value) {
                        $matches = false;
                        break;
                    }
                }

                if ($matches) {
                    $found = true;
                    break;
                }
            }
        }

        Assert::assertTrue(
            $found,
            "Failed asserting that API call was made: {$method} {$endpointPattern}"
        );
    }

    /**
     * Assert that no API calls were made.
     */
    public function assertNoApiCallsMade(): void
    {
        Assert::assertEmpty(
            $this->recorded,
            'Expected no API calls, but ' . count($this->recorded) . ' calls were made'
        );
    }

    /**
     * Assert the number of API calls made.
     */
    public function assertApiCallCount(int $count): void
    {
        Assert::assertCount(
            $count,
            $this->recorded,
            "Expected {$count} API calls, but " . count($this->recorded) . ' calls were made'
        );
    }

    /**
     * Get all recorded API calls.
     *
     * @return array<array{method: string, endpoint: string, data: mixed}>
     */
    public function getRecordedCalls(): array
    {
        return $this->recorded;
    }

    /**
     * Clear all recorded calls.
     */
    public function clearRecordedCalls(): void
    {
        $this->recorded = [];
    }

    /**
     * Get the mock HTTP client for testing purposes.
     *
     * @return HttpClientInterface|\Mockery\MockInterface|null
     */
    public function getMockClient()
    {
        return $this->mockClient;
    }
}
