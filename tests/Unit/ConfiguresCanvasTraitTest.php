<?php

use CanvasLMS\Config;
use CanvasLMS\Laravel\Concerns\ConfiguresCanvas;
use Illuminate\Support\Facades\Log;

it('configures Canvas LMS Kit with API key mode', function () {
    // Create a test class that uses the trait
    $testClass = new class
    {
        use ConfiguresCanvas;

        public function testConfiguration(array $config): void
        {
            $this->applyCanvasConfiguration($config);
        }
    };

    $config = [
        'api_key'     => 'test-api-key',
        'base_url'    => 'https://test.instructure.com',
        'account_id'  => 123456,
        'timeout'     => 30,
        'api_version' => 'v1',
        'auth_mode'   => 'api_key',
    ];

    // Apply configuration
    $testClass->testConfiguration($config);

    // Verify Canvas Config was set correctly
    expect(Config::getApiKey())->toBe('test-api-key')
        ->and(Config::getBaseUrl())->toBe('https://test.instructure.com/')
        ->and(Config::getAccountId())->toBe(123456)
        ->and(Config::getTimeout())->toBe(30)
        ->and(Config::getApiVersion())->toBe('v1');
});

it('configures Canvas LMS Kit with OAuth mode', function () {
    $testClass = new class
    {
        use ConfiguresCanvas;

        public function testConfiguration(array $config): void
        {
            $this->applyCanvasConfiguration($config);
        }
    };

    $config = [
        'base_url'            => 'https://test.instructure.com',
        'auth_mode'           => 'oauth',
        'oauth_client_id'     => 'test-client-id',
        'oauth_client_secret' => 'test-client-secret',
        'oauth_redirect_uri'  => 'https://test.app/callback',
        'oauth_token'         => 'test-token',
        'oauth_refresh_token' => 'test-refresh-token',
    ];

    $testClass->testConfiguration($config);

    expect(Config::getOAuthClientId())->toBe('test-client-id')
        ->and(Config::getOAuthClientSecret())->toBe('test-client-secret')
        ->and(Config::getOAuthRedirectUri())->toBe('https://test.app/callback')
        ->and(Config::getOAuthToken())->toBe('test-token')
        ->and(Config::getOAuthRefreshToken())->toBe('test-refresh-token');
});

it('handles empty config values gracefully', function () {
    $testClass = new class
    {
        use ConfiguresCanvas;

        public function testConfiguration(array $config): void
        {
            $this->applyCanvasConfiguration($config);
        }
    };

    $config = [
        'api_key'     => '',
        'base_url'    => '',
        'log_channel' => '',
        'api_version' => '',
    ];

    // Should not throw any errors
    expect(fn () => $testClass->testConfiguration($config))->not->toThrow(Exception::class);
});

it('handles missing log channel gracefully', function () {
    Log::shouldReceive('channel')
        ->with('nonexistent-channel')
        ->once()
        ->andThrow(new Exception('Channel not found'));

    $testClass = new class
    {
        use ConfiguresCanvas;

        public function testConfiguration(array $config): void
        {
            $this->applyCanvasConfiguration($config);
        }
    };

    $config = [
        'api_key'     => 'test-key',
        'base_url'    => 'https://test.instructure.com',
        'log_channel' => 'nonexistent-channel',
    ];

    // Should not throw any errors, even when log channel fails
    expect(fn () => $testClass->testConfiguration($config))->not->toThrow(Exception::class);
});
