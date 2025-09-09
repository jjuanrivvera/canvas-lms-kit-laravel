<?php

use CanvasLMS\Laravel\Validation\ConfigurationValidator;

describe('ConfigurationValidator', function () {

    beforeEach(function () {
        // Clear validation cache before each test
        ConfigurationValidator::clearValidationCache();
    });

    describe('validateConnection', function () {

        it('validates api_key authentication mode successfully', function () {
            $config = [
                'auth_mode'   => 'api_key',
                'api_key'     => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'    => 'https://test.instructure.com',
                'account_id'  => 1,
                'timeout'     => 30,
                'api_version' => 'v1',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->not->toThrow(InvalidArgumentException::class);
        });

        it('validates oauth authentication mode successfully', function () {
            $config = [
                'auth_mode'           => 'oauth',
                'oauth_client_id'     => 'client_id_123',
                'oauth_client_secret' => 'client_secret_456',
                'oauth_redirect_uri'  => 'https://example.com/callback',
                'base_url'            => 'https://test.instructure.com',
                'account_id'          => 1,
                'timeout'             => 30,
                'api_version'         => 'v1',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->not->toThrow(InvalidArgumentException::class);
        });

        it('throws exception for invalid auth_mode', function () {
            $config = [
                'auth_mode' => 'invalid_mode',
                'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'  => 'https://test.instructure.com',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'auth_mode must be either \'api_key\' or \'oauth\'');
        });

        it('throws exception for non-string auth_mode', function () {
            $config = [
                'auth_mode' => 123,
                'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'  => 'https://test.instructure.com',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'auth_mode must be a string');
        });

        it('defaults to api_key when auth_mode is not specified', function () {
            $config = [
                'api_key'  => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url' => 'https://test.instructure.com',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->not->toThrow(InvalidArgumentException::class);
        });
    });

    describe('API key validation', function () {

        it('throws exception when api_key is missing in api_key mode', function () {
            $config = [
                'auth_mode' => 'api_key',
                'base_url'  => 'https://test.instructure.com',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'api_key is required when using \'api_key\' authentication mode');
        });

        it('throws exception when api_key is empty string', function () {
            $config = [
                'auth_mode' => 'api_key',
                'api_key'   => '',
                'base_url'  => 'https://test.instructure.com',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'api_key is required when using \'api_key\' authentication mode');
        });

        it('throws exception when api_key is not a string', function () {
            $config = [
                'auth_mode' => 'api_key',
                'api_key'   => 123456,
                'base_url'  => 'https://test.instructure.com',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'api_key must be a string');
        });

        it('throws exception when api_key is too short', function () {
            $config = [
                'auth_mode' => 'api_key',
                'api_key'   => 'short_key',
                'base_url'  => 'https://test.instructure.com',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'api_key appears to be too short');
        });

        it('throws exception when api_key contains invalid characters', function () {
            $config = [
                'auth_mode' => 'api_key',
                'api_key'   => 'invalid_key_with_@#$%_special_chars',
                'base_url'  => 'https://test.instructure.com',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'api_key contains invalid characters');
        });
    });

    describe('OAuth validation', function () {

        it('throws exception when oauth_client_id is missing', function () {
            $config = [
                'auth_mode'           => 'oauth',
                'oauth_client_secret' => 'client_secret',
                'oauth_redirect_uri'  => 'https://example.com/callback',
                'base_url'            => 'https://test.instructure.com',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'oauth_client_id is required when using \'oauth\' authentication mode');
        });

        it('throws exception when oauth_client_secret is missing', function () {
            $config = [
                'auth_mode'          => 'oauth',
                'oauth_client_id'    => 'client_id',
                'oauth_redirect_uri' => 'https://example.com/callback',
                'base_url'           => 'https://test.instructure.com',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'oauth_client_secret is required when using \'oauth\' authentication mode');
        });

        it('throws exception when oauth_redirect_uri is missing', function () {
            $config = [
                'auth_mode'           => 'oauth',
                'oauth_client_id'     => 'client_id',
                'oauth_client_secret' => 'client_secret',
                'base_url'            => 'https://test.instructure.com',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'oauth_redirect_uri is required when using \'oauth\' authentication mode');
        });

        it('throws exception when oauth_redirect_uri is not a valid URL', function () {
            $config = [
                'auth_mode'           => 'oauth',
                'oauth_client_id'     => 'client_id',
                'oauth_client_secret' => 'client_secret',
                'oauth_redirect_uri'  => 'not-a-valid-url',
                'base_url'            => 'https://test.instructure.com',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'oauth_redirect_uri must be a valid URL');
        });

        it('throws exception when oauth_redirect_uri uses HTTP (not HTTPS or localhost)', function () {
            $config = [
                'auth_mode'           => 'oauth',
                'oauth_client_id'     => 'client_id',
                'oauth_client_secret' => 'client_secret',
                'oauth_redirect_uri'  => 'http://example.com/callback',
                'base_url'            => 'https://test.instructure.com',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'oauth_redirect_uri must use HTTPS or be localhost');
        });

        it('allows HTTP for localhost development', function () {
            $config = [
                'auth_mode'           => 'oauth',
                'oauth_client_id'     => 'client_id',
                'oauth_client_secret' => 'client_secret',
                'oauth_redirect_uri'  => 'http://localhost:8000/callback',
                'base_url'            => 'https://test.instructure.com',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->not->toThrow(InvalidArgumentException::class);
        });
    });

    describe('Base URL validation', function () {

        it('throws exception when base_url is missing', function () {
            $config = [
                'auth_mode' => 'api_key',
                'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'base_url is required');
        });

        it('throws exception when base_url is empty string', function () {
            $config = [
                'auth_mode' => 'api_key',
                'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'  => '',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'base_url is required');
        });

        it('throws exception when base_url is not a string', function () {
            $config = [
                'auth_mode' => 'api_key',
                'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'  => 12345,
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'base_url must be a string');
        });

        it('throws exception when base_url is not a valid URL', function () {
            $config = [
                'auth_mode' => 'api_key',
                'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'  => 'not-a-valid-url',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'base_url must be a valid URL');
        });

        it('throws exception when base_url uses HTTP (not HTTPS or localhost)', function () {
            $config = [
                'auth_mode' => 'api_key',
                'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'  => 'http://canvas.example.com',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'base_url must use HTTPS for security');
        });

        it('allows HTTP for localhost development', function () {
            $config = [
                'auth_mode' => 'api_key',
                'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'  => 'http://localhost:3000',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->not->toThrow(InvalidArgumentException::class);
        });

        it('accepts valid Canvas URLs', function () {
            $validUrls = [
                'https://test.instructure.com',
                'https://school.canvaslms.com',
                'https://my-canvas-instance.com',
                'https://localhost:3000/canvas',
            ];

            foreach ($validUrls as $url) {
                $config = [
                    'auth_mode' => 'api_key',
                    'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                    'base_url'  => $url,
                ];

                expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                    ->not->toThrow(InvalidArgumentException::class);
            }
        });

        it('throws exception for suspicious non-Canvas URLs', function () {
            $config = [
                'auth_mode' => 'api_key',
                'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'  => 'https://evil-site.com',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'base_url does not appear to be a valid Canvas LMS URL');
        });
    });

    describe('Account ID validation', function () {

        it('allows valid integer account_id', function () {
            $config = [
                'auth_mode'  => 'api_key',
                'api_key'    => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'   => 'https://test.instructure.com',
                'account_id' => 1,
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->not->toThrow(InvalidArgumentException::class);
        });

        it('allows numeric string account_id', function () {
            $config = [
                'auth_mode'  => 'api_key',
                'api_key'    => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'   => 'https://test.instructure.com',
                'account_id' => '1',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->not->toThrow(InvalidArgumentException::class);
        });

        it('throws exception for non-numeric account_id', function () {
            $config = [
                'auth_mode'  => 'api_key',
                'api_key'    => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'   => 'https://test.instructure.com',
                'account_id' => 'not-a-number',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'account_id must be a positive integer');
        });

        it('throws exception for negative account_id', function () {
            $config = [
                'auth_mode'  => 'api_key',
                'api_key'    => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'   => 'https://test.instructure.com',
                'account_id' => -1,
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'account_id must be a positive integer greater than 0');
        });

        it('throws exception for zero account_id', function () {
            $config = [
                'auth_mode'  => 'api_key',
                'api_key'    => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'   => 'https://test.instructure.com',
                'account_id' => 0,
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'account_id must be a positive integer greater than 0');
        });

        it('allows missing account_id (optional field)', function () {
            $config = [
                'auth_mode' => 'api_key',
                'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'  => 'https://test.instructure.com',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->not->toThrow(InvalidArgumentException::class);
        });
    });

    describe('Timeout validation', function () {

        it('allows valid timeout values', function () {
            $validTimeouts = [1, 30, 60, 120, 300];

            foreach ($validTimeouts as $timeout) {
                $config = [
                    'auth_mode' => 'api_key',
                    'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                    'base_url'  => 'https://test.instructure.com',
                    'timeout'   => $timeout,
                ];

                expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                    ->not->toThrow(InvalidArgumentException::class);
            }
        });

        it('allows numeric string timeout', function () {
            $config = [
                'auth_mode' => 'api_key',
                'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'  => 'https://test.instructure.com',
                'timeout'   => '30',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->not->toThrow(InvalidArgumentException::class);
        });

        it('throws exception for non-numeric timeout', function () {
            $config = [
                'auth_mode' => 'api_key',
                'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'  => 'https://test.instructure.com',
                'timeout'   => 'not-a-number',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'timeout must be an integer');
        });

        it('throws exception for timeout less than 1', function () {
            $config = [
                'auth_mode' => 'api_key',
                'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'  => 'https://test.instructure.com',
                'timeout'   => 0,
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'timeout must be between 1 and 300 seconds');
        });

        it('throws exception for timeout greater than 300', function () {
            $config = [
                'auth_mode' => 'api_key',
                'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'  => 'https://test.instructure.com',
                'timeout'   => 301,
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'timeout must be between 1 and 300 seconds');
        });

        it('allows missing timeout (optional field)', function () {
            $config = [
                'auth_mode' => 'api_key',
                'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'  => 'https://test.instructure.com',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->not->toThrow(InvalidArgumentException::class);
        });
    });

    describe('API version validation', function () {

        it('allows valid API versions', function () {
            $validVersions = ['v1', 'v2', 'v3', 'beta'];

            foreach ($validVersions as $version) {
                $config = [
                    'auth_mode'   => 'api_key',
                    'api_key'     => 'test_api_key_1234567890abcdef1234567890abcdef',
                    'base_url'    => 'https://test.instructure.com',
                    'api_version' => $version,
                ];

                expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                    ->not->toThrow(InvalidArgumentException::class);
            }
        });

        it('throws exception for non-string api_version', function () {
            $config = [
                'auth_mode'   => 'api_key',
                'api_key'     => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'    => 'https://test.instructure.com',
                'api_version' => 123,
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'api_version must be a string');
        });

        it('throws exception for invalid api_version format', function () {
            $config = [
                'auth_mode'   => 'api_key',
                'api_key'     => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'    => 'https://test.instructure.com',
                'api_version' => 'invalid',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->toThrow(InvalidArgumentException::class, 'api_version must be in format \'v1\', \'v2\', or \'beta\'');
        });

        it('allows missing api_version (optional field)', function () {
            $config = [
                'auth_mode' => 'api_key',
                'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                'base_url'  => 'https://test.instructure.com',
            ];

            expect(fn () => ConfigurationValidator::validateConnection($config, 'test'))
                ->not->toThrow(InvalidArgumentException::class);
        });
    });

    describe('validateCanvasConfiguration', function () {

        it('validates a complete configuration successfully', function () {
            $config = [
                'default'     => 'main',
                'connections' => [
                    'main' => [
                        'auth_mode'   => 'api_key',
                        'api_key'     => 'test_api_key_1234567890abcdef1234567890abcdef',
                        'base_url'    => 'https://test.instructure.com',
                        'account_id'  => 1,
                        'timeout'     => 30,
                        'api_version' => 'v1',
                    ],
                ],
            ];

            expect(fn () => ConfigurationValidator::validateCanvasConfiguration($config))
                ->not->toThrow(InvalidArgumentException::class);
        });

        it('throws exception when default connection is missing', function () {
            $config = [
                'connections' => [
                    'main' => [
                        'auth_mode' => 'api_key',
                        'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                        'base_url'  => 'https://test.instructure.com',
                    ],
                ],
            ];

            expect(fn () => ConfigurationValidator::validateCanvasConfiguration($config))
                ->toThrow(InvalidArgumentException::class, '\'default\' connection name is required');
        });

        it('throws exception when default connection is not a string', function () {
            $config = [
                'default'     => 123,
                'connections' => [
                    'main' => [
                        'auth_mode' => 'api_key',
                        'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                        'base_url'  => 'https://test.instructure.com',
                    ],
                ],
            ];

            expect(fn () => ConfigurationValidator::validateCanvasConfiguration($config))
                ->toThrow(InvalidArgumentException::class, '\'default\' must be a string');
        });

        it('throws exception when connections array is missing', function () {
            $config = [
                'default' => 'main',
            ];

            expect(fn () => ConfigurationValidator::validateCanvasConfiguration($config))
                ->toThrow(InvalidArgumentException::class, '\'connections\' must be an array');
        });

        it('throws exception when connections array is empty', function () {
            $config = [
                'default'     => 'main',
                'connections' => [],
            ];

            expect(fn () => ConfigurationValidator::validateCanvasConfiguration($config))
                ->toThrow(InvalidArgumentException::class, 'at least one connection must be configured');
        });

        it('throws exception when default connection is not configured', function () {
            $config = [
                'default'     => 'missing',
                'connections' => [
                    'main' => [
                        'auth_mode' => 'api_key',
                        'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                        'base_url'  => 'https://test.instructure.com',
                    ],
                ],
            ];

            expect(fn () => ConfigurationValidator::validateCanvasConfiguration($config))
                ->toThrow(InvalidArgumentException::class, 'default connection \'missing\' is not configured');
        });

        it('throws exception when a connection configuration is not an array', function () {
            $config = [
                'default'     => 'main',
                'connections' => [
                    'main' => 'not-an-array',
                ],
            ];

            expect(fn () => ConfigurationValidator::validateCanvasConfiguration($config))
                ->toThrow(InvalidArgumentException::class, 'connection \'main\' must be an array');
        });

        it('validates multiple connections successfully', function () {
            $config = [
                'default'     => 'main',
                'connections' => [
                    'main' => [
                        'auth_mode' => 'api_key',
                        'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                        'base_url'  => 'https://main.instructure.com',
                    ],
                    'secondary' => [
                        'auth_mode'           => 'oauth',
                        'oauth_client_id'     => 'client_id',
                        'oauth_client_secret' => 'client_secret',
                        'oauth_redirect_uri'  => 'https://example.com/callback',
                        'base_url'            => 'https://secondary.instructure.com',
                    ],
                ],
            ];

            expect(fn () => ConfigurationValidator::validateCanvasConfiguration($config))
                ->not->toThrow(InvalidArgumentException::class);
        });
    });

    describe('Performance optimization features', function () {

        it('validates with caching enabled by default', function () {
            $config = [
                'default'     => 'main',
                'connections' => [
                    'main' => [
                        'auth_mode'  => 'api_key',
                        'api_key'    => 'test_api_key_1234567890abcdef1234567890abcdef',
                        'base_url'   => 'https://test.instructure.com',
                        'account_id' => 1,
                    ],
                ],
            ];

            expect(fn () => ConfigurationValidator::validateCanvasConfigurationOptimized($config))
                ->not->toThrow(InvalidArgumentException::class);
        });

        it('validates with caching disabled', function () {
            $config = [
                'default'     => 'main',
                'connections' => [
                    'main' => [
                        'auth_mode'  => 'api_key',
                        'api_key'    => 'test_api_key_1234567890abcdef1234567890abcdef',
                        'base_url'   => 'https://test.instructure.com',
                        'account_id' => 1,
                    ],
                ],
            ];

            expect(fn () => ConfigurationValidator::validateCanvasConfigurationOptimized($config, false))
                ->not->toThrow(InvalidArgumentException::class);
        });

        it('caches validation results for identical configurations', function () {
            $config = [
                'default'     => 'main',
                'connections' => [
                    'main' => [
                        'auth_mode'  => 'api_key',
                        'api_key'    => 'test_api_key_1234567890abcdef1234567890abcdef',
                        'base_url'   => 'https://test.instructure.com',
                        'account_id' => 1,
                    ],
                ],
            ];

            // First validation
            ConfigurationValidator::validateCanvasConfigurationOptimized($config, true);

            // Second validation should use cache
            expect(fn () => ConfigurationValidator::validateCanvasConfigurationOptimized($config, true))
                ->not->toThrow(InvalidArgumentException::class);
        });

        it('throws error for invalid configuration even with caching', function () {
            $config = [
                'default'     => 'main',
                'connections' => [
                    'main' => [
                        'auth_mode' => 'api_key',
                        'api_key'   => '', // Invalid
                        'base_url'  => 'https://test.instructure.com',
                    ],
                ],
            ];

            expect(fn () => ConfigurationValidator::validateCanvasConfigurationOptimized($config, true))
                ->toThrow(InvalidArgumentException::class);
        });

        it('clears validation cache successfully', function () {
            $config = [
                'default'     => 'main',
                'connections' => [
                    'main' => [
                        'auth_mode'  => 'api_key',
                        'api_key'    => 'test_api_key_1234567890abcdef1234567890abcdef',
                        'base_url'   => 'https://test.instructure.com',
                        'account_id' => 1,
                    ],
                ],
            ];

            // Validate to populate cache
            ConfigurationValidator::validateCanvasConfigurationOptimized($config, true);

            // Clear cache
            ConfigurationValidator::clearValidationCache();

            // Should still validate successfully after cache clear
            expect(fn () => ConfigurationValidator::validateCanvasConfigurationOptimized($config, true))
                ->not->toThrow(InvalidArgumentException::class);
        });

        it('handles different configurations with separate cache entries', function () {
            $config1 = [
                'default'     => 'main',
                'connections' => [
                    'main' => [
                        'auth_mode' => 'api_key',
                        'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                        'base_url'  => 'https://test1.instructure.com',
                    ],
                ],
            ];

            $config2 = [
                'default'     => 'main',
                'connections' => [
                    'main' => [
                        'auth_mode' => 'api_key',
                        'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                        'base_url'  => 'https://test2.instructure.com',
                    ],
                ],
            ];

            // Both configurations should be validated and cached separately
            expect(fn () => ConfigurationValidator::validateCanvasConfigurationOptimized($config1, true))
                ->not->toThrow(InvalidArgumentException::class);

            expect(fn () => ConfigurationValidator::validateCanvasConfigurationOptimized($config2, true))
                ->not->toThrow(InvalidArgumentException::class);
        });
    });
});
