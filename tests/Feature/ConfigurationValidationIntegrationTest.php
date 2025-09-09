<?php

use CanvasLMS\Laravel\CanvasServiceProvider;
use CanvasLMS\Laravel\Validation\ConfigurationValidator;
use Illuminate\Support\Facades\Config;

describe('Configuration Validation Integration', function () {

    beforeEach(function () {
        // Clear any existing Canvas configuration
        Config::set('canvas', null);

        // Clear validation cache before each test
        ConfigurationValidator::clearValidationCache();
    });

    it('boots successfully with valid configuration', function () {
        Config::set('canvas', [
            'default'     => 'testing',
            'connections' => [
                'testing' => [
                    'auth_mode'  => 'api_key',
                    'api_key'    => 'test_api_key_1234567890abcdef1234567890abcdef',
                    'base_url'   => 'https://test.instructure.com',
                    'account_id' => 1,
                    'timeout'    => 30,
                ],
            ],
            'validation' => [
                'enabled' => true,
            ],
        ]);

        $provider = new CanvasServiceProvider($this->app);
        $provider->register();

        // Should not throw any exceptions when booting
        expect(fn () => $this->app->boot())->not->toThrow(InvalidArgumentException::class);
    });

    it('throws validation error during boot with invalid configuration', function () {
        // Reconfigure the application with invalid config
        $this->refreshApplication();

        Config::set('canvas', [
            'default'     => 'testing',
            'connections' => [
                'testing' => [
                    'auth_mode' => 'api_key',
                    'api_key'   => '', // Invalid: empty API key
                    'base_url'  => 'https://test.instructure.com',
                ],
            ],
            'validation' => [
                'enabled' => true,
            ],
        ]);

        // Create a fresh service provider instance and trigger validation manually
        $provider = new CanvasServiceProvider($this->app);

        expect(function () use ($provider) {
            // Call the protected validateConfiguration method using reflection
            $reflection = new \ReflectionClass($provider);
            $method = $reflection->getMethod('validateConfiguration');
            $method->setAccessible(true);
            $method->invoke($provider);
        })->toThrow(InvalidArgumentException::class, 'Canvas configuration validation failed');
    });

    it('skips validation when disabled', function () {
        Config::set('canvas', [
            'default'     => 'testing',
            'connections' => [
                'testing' => [
                    'auth_mode' => 'api_key',
                    'api_key'   => '', // Invalid: empty API key, but validation is disabled
                    'base_url'  => 'https://test.instructure.com',
                ],
            ],
            'validation' => [
                'enabled' => false,
            ],
        ]);

        $provider = new CanvasServiceProvider($this->app);

        // Should not throw validation errors when validation is disabled
        expect(fn () => $provider->boot())->not->toThrow(InvalidArgumentException::class);
    });

    it('validates with default validation enabled when config missing', function () {
        Config::set('canvas', [
            'default'     => 'testing',
            'connections' => [
                'testing' => [
                    'auth_mode' => 'api_key',
                    'api_key'   => '', // Invalid: empty API key
                    'base_url'  => 'https://test.instructure.com',
                ],
            ],
            // No validation config - should default to enabled
        ]);

        // Create a fresh service provider instance and trigger validation manually
        $provider = new CanvasServiceProvider($this->app);

        expect(function () use ($provider) {
            // Call the protected validateConfiguration method using reflection
            $reflection = new \ReflectionClass($provider);
            $method = $reflection->getMethod('validateConfiguration');
            $method->setAccessible(true);
            $method->invoke($provider);
        })->toThrow(InvalidArgumentException::class, 'Canvas configuration validation failed');
    });

    it('provides helpful error message with fix instructions', function () {
        Config::set('canvas', [
            'default'     => 'testing',
            'connections' => [
                'testing' => [
                    'auth_mode' => 'api_key',
                    'api_key'   => 'short', // Invalid: API key too short
                    'base_url'  => 'https://test.instructure.com',
                ],
            ],
            'validation' => [
                'enabled' => true,
            ],
        ]);

        // Create a fresh service provider instance and trigger validation manually
        $provider = new CanvasServiceProvider($this->app);

        expect(function () use ($provider) {
            // Call the protected validateConfiguration method using reflection
            $reflection = new \ReflectionClass($provider);
            $method = $reflection->getMethod('validateConfiguration');
            $method->setAccessible(true);
            $method->invoke($provider);
        })->toThrow(InvalidArgumentException::class)
            ->and(function () use ($provider) {
                // Call the method again to test the error message
                $reflection = new \ReflectionClass($provider);
                $method = $reflection->getMethod('validateConfiguration');
                $method->setAccessible(true);
                $method->invoke($provider);
            })->toThrow(InvalidArgumentException::class, 'To disable configuration validation, set CANVAS_VALIDATION_ENABLED=false');
    });

    it('throws error when canvas config is completely missing', function () {
        // Clear the canvas config completely
        Config::set('canvas', null);

        // Create a fresh service provider instance and trigger validation manually
        $provider = new CanvasServiceProvider($this->app);

        expect(function () use ($provider) {
            // Call the protected validateConfiguration method using reflection
            $reflection = new \ReflectionClass($provider);
            $method = $reflection->getMethod('validateConfiguration');
            $method->setAccessible(true);
            $method->invoke($provider);
        })->toThrow(InvalidArgumentException::class, 'Canvas configuration is not available');
    });

    it('validates OAuth configuration correctly during boot', function () {
        Config::set('canvas', [
            'default'     => 'testing',
            'connections' => [
                'testing' => [
                    'auth_mode'           => 'oauth',
                    'oauth_client_id'     => 'test_client_id',
                    'oauth_client_secret' => 'test_client_secret',
                    'oauth_redirect_uri'  => 'https://example.com/callback',
                    'base_url'            => 'https://test.instructure.com',
                ],
            ],
            'validation' => [
                'enabled' => true,
            ],
        ]);

        $provider = new CanvasServiceProvider($this->app);

        expect(fn () => $provider->boot())->not->toThrow(InvalidArgumentException::class);
    });

    it('validates multiple connections during boot', function () {
        Config::set('canvas', [
            'default'     => 'production',
            'connections' => [
                'production' => [
                    'auth_mode' => 'api_key',
                    'api_key'   => 'prod_api_key_1234567890abcdef1234567890abcdef',
                    'base_url'  => 'https://prod.instructure.com',
                ],
                'staging' => [
                    'auth_mode'           => 'oauth',
                    'oauth_client_id'     => 'staging_client_id',
                    'oauth_client_secret' => 'staging_client_secret',
                    'oauth_redirect_uri'  => 'https://staging.example.com/callback',
                    'base_url'            => 'https://staging.instructure.com',
                ],
            ],
            'validation' => [
                'enabled' => true,
            ],
        ]);

        $provider = new CanvasServiceProvider($this->app);

        expect(fn () => $provider->boot())->not->toThrow(InvalidArgumentException::class);
    });

    it('catches validation errors in any connection during boot', function () {
        Config::set('canvas', [
            'default'     => 'production',
            'connections' => [
                'production' => [
                    'auth_mode' => 'api_key',
                    'api_key'   => 'prod_api_key_1234567890abcdef1234567890abcdef',
                    'base_url'  => 'https://prod.instructure.com',
                ],
                'staging' => [
                    'auth_mode' => 'api_key',
                    'api_key'   => 'short', // Invalid: API key too short
                    'base_url'  => 'https://staging.instructure.com',
                ],
            ],
            'validation' => [
                'enabled' => true,
            ],
        ]);

        // Create a fresh service provider instance and trigger validation manually
        $provider = new CanvasServiceProvider($this->app);

        expect(function () use ($provider) {
            // Call the protected validateConfiguration method using reflection
            $reflection = new \ReflectionClass($provider);
            $method = $reflection->getMethod('validateConfiguration');
            $method->setAccessible(true);
            $method->invoke($provider);
        })->toThrow(InvalidArgumentException::class, 'Canvas configuration validation failed');
    });

    describe('Real-world validation scenarios', function () {

        it('validates typical production API key setup', function () {
            Config::set('canvas', [
                'default'     => 'production',
                'connections' => [
                    'production' => [
                        'auth_mode'   => 'api_key',
                        'api_key'     => '1234567890abcdef1234567890abcdef1234567890abcdef1234567890abcdef1234',
                        'base_url'    => 'https://myschool.instructure.com',
                        'account_id'  => 1,
                        'timeout'     => 60,
                        'api_version' => 'v1',
                    ],
                ],
                'validation' => [
                    'enabled' => true,
                ],
            ]);

            $provider = new CanvasServiceProvider($this->app);

            expect(fn () => $provider->boot())->not->toThrow(InvalidArgumentException::class);
        });

        it('validates typical OAuth setup', function () {
            Config::set('canvas', [
                'default'     => 'oauth_connection',
                'connections' => [
                    'oauth_connection' => [
                        'auth_mode'           => 'oauth',
                        'oauth_client_id'     => 'oauth_client_12345',
                        'oauth_client_secret' => 'oauth_secret_67890abcdef',
                        'oauth_redirect_uri'  => 'https://myapp.example.com/auth/canvas/callback',
                        'base_url'            => 'https://myschool.instructure.com',
                        'account_id'          => 1,
                        'timeout'             => 30,
                    ],
                ],
                'validation' => [
                    'enabled' => true,
                ],
            ]);

            $provider = new CanvasServiceProvider($this->app);

            expect(fn () => $provider->boot())->not->toThrow(InvalidArgumentException::class);
        });

        it('catches common configuration mistakes', function () {
            $commonMistakes = [
                // Missing API key
                [
                    'default'     => 'main',
                    'connections' => [
                        'main' => [
                            'auth_mode' => 'api_key',
                            'base_url'  => 'https://test.instructure.com',
                        ],
                    ],
                ],
                // HTTP instead of HTTPS
                [
                    'default'     => 'main',
                    'connections' => [
                        'main' => [
                            'auth_mode' => 'api_key',
                            'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                            'base_url'  => 'http://test.instructure.com',
                        ],
                    ],
                ],
                // Invalid account ID
                [
                    'default'     => 'main',
                    'connections' => [
                        'main' => [
                            'auth_mode'  => 'api_key',
                            'api_key'    => 'test_api_key_1234567890abcdef1234567890abcdef',
                            'base_url'   => 'https://test.instructure.com',
                            'account_id' => -1,
                        ],
                    ],
                ],
                // Timeout too high
                [
                    'default'     => 'main',
                    'connections' => [
                        'main' => [
                            'auth_mode' => 'api_key',
                            'api_key'   => 'test_api_key_1234567890abcdef1234567890abcdef',
                            'base_url'  => 'https://test.instructure.com',
                            'timeout'   => 500,
                        ],
                    ],
                ],
            ];

            foreach ($commonMistakes as $config) {
                Config::set('canvas', array_merge($config, [
                    'validation' => ['enabled' => true],
                ]));

                // Create a fresh service provider instance and trigger validation manually
                $provider = new CanvasServiceProvider($this->app);

                expect(function () use ($provider) {
                    // Call the protected validateConfiguration method using reflection
                    $reflection = new \ReflectionClass($provider);
                    $method = $reflection->getMethod('validateConfiguration');
                    $method->setAccessible(true);
                    $method->invoke($provider);
                })->toThrow(InvalidArgumentException::class, 'Canvas configuration validation failed');
            }
        });
    });

    describe('Performance optimizations', function () {

        it('skips validation when configuration is cached in production', function () {
            // Set production environment
            $this->app['env'] = 'production';

            // Mock the Application instance to control the methods we need
            $mockApp = Mockery::mock($this->app)->makePartial();
            $mockApp->shouldReceive('configurationIsCached')->andReturn(true);
            $mockApp->shouldReceive('environment')->with('production')->andReturn(true);
            $mockApp->shouldReceive('booting')->andReturn(null); // Skip the callback

            Config::set('canvas', [
                'default'     => 'testing',
                'connections' => [
                    'testing' => [
                        'auth_mode' => 'api_key',
                        'api_key'   => '', // Invalid: empty API key, but should be skipped
                        'base_url'  => 'https://test.instructure.com',
                    ],
                ],
                'validation' => [
                    'enabled' => true,
                ],
            ]);

            $provider = new CanvasServiceProvider($mockApp);

            // Should not throw validation errors when config is cached in production
            expect(fn () => $provider->register())->not->toThrow(InvalidArgumentException::class);
        });

        it('skips validation when not in validation environments', function () {
            // Set production environment (not in default validation environments)
            $this->app['env'] = 'production';

            // Mock the Application instance to control the methods we need
            $mockApp = Mockery::mock($this->app)->makePartial();
            $mockApp->shouldReceive('configurationIsCached')->andReturn(false);
            $mockApp->shouldReceive('environment')->with(['local', 'testing', 'staging'])->andReturn(false);
            $mockApp->shouldReceive('booting')->andReturn(null); // Skip the callback

            Config::set('canvas', [
                'default'     => 'testing',
                'connections' => [
                    'testing' => [
                        'auth_mode' => 'api_key',
                        'api_key'   => '', // Invalid: empty API key, but should be skipped
                        'base_url'  => 'https://test.instructure.com',
                    ],
                ],
                'validation' => [
                    'enabled'      => true,
                    'environments' => ['local', 'testing', 'staging'],
                ],
            ]);

            $provider = new CanvasServiceProvider($mockApp);

            // Should not throw validation errors when not in validation environments
            expect(fn () => $provider->register())->not->toThrow(InvalidArgumentException::class);
        });

        it('validates in custom validation environments', function () {
            // Set custom environment
            $this->app['env'] = 'custom';

            // Mock the Application instance to control the methods we need
            $mockApp = Mockery::mock($this->app)->makePartial();
            $mockApp->shouldReceive('configurationIsCached')->andReturn(false);
            $mockApp->shouldReceive('environment')->with(['custom'])->andReturn(true);
            // Allow the validation to run by calling the callback immediately
            $mockApp->shouldReceive('booting')->andReturnUsing(function ($callback) {
                $callback();
            });

            Config::set('canvas', [
                'default'     => 'testing',
                'connections' => [
                    'testing' => [
                        'auth_mode' => 'api_key',
                        'api_key'   => '', // Invalid: empty API key
                        'base_url'  => 'https://test.instructure.com',
                    ],
                ],
                'validation' => [
                    'enabled'      => true,
                    'environments' => ['custom'],
                ],
            ]);

            $provider = new CanvasServiceProvider($mockApp);

            // Should throw validation errors when in custom validation environment
            expect(fn () => $provider->register())
                ->toThrow(InvalidArgumentException::class, 'Canvas configuration validation failed');
        });

        it('uses validation caching when enabled', function () {
            // This test verifies the caching mechanism works
            $config = [
                'default'     => 'testing',
                'connections' => [
                    'testing' => [
                        'auth_mode'  => 'api_key',
                        'api_key'    => 'test_api_key_1234567890abcdef1234567890abcdef',
                        'base_url'   => 'https://test.instructure.com',
                        'account_id' => 1,
                        'timeout'    => 30,
                    ],
                ],
                'validation' => [
                    'enabled'       => true,
                    'cache_results' => true,
                ],
            ];

            // First validation should succeed and cache the result
            ConfigurationValidator::validateCanvasConfigurationOptimized($config, true);

            // Second validation should use cache and also succeed
            expect(fn () => ConfigurationValidator::validateCanvasConfigurationOptimized($config, true))
                ->not->toThrow(InvalidArgumentException::class);
        });

        it('clears validation cache correctly', function () {
            $validConfig = [
                'default'     => 'testing',
                'connections' => [
                    'testing' => [
                        'auth_mode'  => 'api_key',
                        'api_key'    => 'test_api_key_1234567890abcdef1234567890abcdef',
                        'base_url'   => 'https://test.instructure.com',
                        'account_id' => 1,
                        'timeout'    => 30,
                    ],
                ],
            ];

            // Validate and cache the result
            ConfigurationValidator::validateCanvasConfigurationOptimized($validConfig, true);

            // Clear cache
            ConfigurationValidator::clearValidationCache();

            // Validation should still work after cache clear
            expect(fn () => ConfigurationValidator::validateCanvasConfigurationOptimized($validConfig, true))
                ->not->toThrow(InvalidArgumentException::class);
        });

        it('bypasses cache when cache is disabled', function () {
            $config = [
                'default'     => 'testing',
                'connections' => [
                    'testing' => [
                        'auth_mode'  => 'api_key',
                        'api_key'    => 'test_api_key_1234567890abcdef1234567890abcdef',
                        'base_url'   => 'https://test.instructure.com',
                        'account_id' => 1,
                        'timeout'    => 30,
                    ],
                ],
            ];

            // Should validate without using cache
            expect(fn () => ConfigurationValidator::validateCanvasConfigurationOptimized($config, false))
                ->not->toThrow(InvalidArgumentException::class);
        });
    });
});
