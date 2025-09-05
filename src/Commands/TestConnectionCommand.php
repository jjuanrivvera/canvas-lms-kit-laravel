<?php

namespace CanvasLMS\Laravel\Commands;

use CanvasLMS\Api\Users\User;
use CanvasLMS\Config;
use CanvasLMS\Laravel\CanvasManager;
use Exception;
use Illuminate\Console\Command;

class TestConnectionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'canvas:test 
                            {--connection= : The connection to test (defaults to the default connection)}
                            {--show-config : Display the current configuration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the Canvas LMS API connection and display user information';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $connection = $this->option('connection') ?? config('canvas.default', 'main');

        $this->info('🔍 Testing Canvas LMS Connection: ' . $connection);
        $this->newLine();

        // Switch to the specified connection if not default
        if ($connection !== config('canvas.default')) {
            try {
                /** @var CanvasManager $canvas */
                $canvas = app(CanvasManager::class);
                $canvas->connection($connection);
            } catch (Exception $e) {
                $this->error('❌ Failed to switch connection: ' . $e->getMessage());

                return self::FAILURE;
            }
        }

        // Show configuration if requested
        if ($this->option('show-config') === true) {
            $this->displayConfiguration($connection);
        }

        // Test the connection
        $this->info('Attempting to connect to Canvas LMS...');

        try {
            // Try to get the current user (self)
            $user = User::self();

            $this->newLine();
            $this->info('✅ Connection successful!');
            $this->newLine();

            // Display user information
            $this->displayUserInfo($user);

            // Test additional endpoints if verbose
            if ($this->output->isVerbose()) {
                $this->testAdditionalEndpoints();
            }

            return self::SUCCESS;

        } catch (Exception $e) {
            $this->newLine();
            $this->error('❌ Connection failed!');
            $this->error('Error: ' . $e->getMessage());
            $this->newLine();

            // Provide debugging tips
            $this->provideTroubleshootingTips($e);

            return self::FAILURE;
        }
    }

    /**
     * Display the current configuration.
     */
    protected function displayConfiguration(string $connection): void
    {
        $config = config("canvas.connections.{$connection}");

        if ($config === null) {
            $this->warn('No configuration found for connection: ' . $connection);

            return;
        }

        $this->info('📋 Current Configuration:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Base URL', $config['base_url'] ?? 'Not set'],
                ['API Key', $this->maskApiKey($config['api_key'] ?? '')],
                ['Account ID', $config['account_id'] ?? '1'],
                ['Timeout', ($config['timeout'] ?? 30) . ' seconds'],
                ['API Version', $config['api_version'] ?? 'v1'],
                ['Log Channel', $config['log_channel'] ?? 'Not configured'],
            ]
        );
        $this->newLine();
    }

    /**
     * Display user information.
     */
    protected function displayUserInfo(User $user): void
    {
        $this->info('👤 Authenticated User Information:');

        $userData = [
            ['ID', $user->id ?? 'N/A'],
            ['Name', $user->name ?? 'N/A'],
            ['Email', $user->email ?? $user->login_id ?? 'N/A'],
            ['SIS User ID', $user->sis_user_id ?? 'Not available'],
            ['Integration ID', $user->integration_id ?? 'Not available'],
            ['Time Zone', $user->time_zone ?? 'Not set'],
        ];

        // Add additional fields if they exist
        if (isset($user->avatar_url)) {
            $userData[] = ['Avatar', '✓ Available'];
        }

        if (isset($user->locale)) {
            $userData[] = ['Locale', $user->locale];
        }

        $this->table(['Field', 'Value'], $userData);
    }

    /**
     * Test additional endpoints when verbose mode is enabled.
     */
    protected function testAdditionalEndpoints(): void
    {
        $this->newLine();
        $this->info('🧪 Testing Additional Endpoints (Verbose Mode):');
        $this->newLine();

        $endpoints = [
            'Courses' => function () {
                $courses = \CanvasLMS\Api\Courses\Course::get(['per_page' => 1]);

                return count($courses) > 0 ? '✅ Accessible' : '⚠️ No courses found';
            },
            'Accounts' => function () {
                try {
                    $account = \CanvasLMS\Api\Accounts\Account::find(Config::getAccountId());

                    return '✅ Accessible';
                } catch (Exception $e) {
                    return '❌ ' . $this->getErrorType($e);
                }
            },
            'Enrollments' => function () {
                $enrollments = \CanvasLMS\Api\Enrollments\Enrollment::get(['per_page' => 1]);

                return count($enrollments) > 0 ? '✅ Accessible' : '⚠️ No enrollments found';
            },
        ];

        $results = [];
        foreach ($endpoints as $name => $test) {
            try {
                $result = $test();
                $results[] = [$name, $result];
            } catch (Exception $e) {
                $results[] = [$name, '❌ ' . $this->getErrorType($e)];
            }
        }

        $this->table(['Endpoint', 'Status'], $results);
    }

    /**
     * Provide troubleshooting tips based on the error.
     */
    protected function provideTroubleshootingTips(Exception $e): void
    {
        $this->info('💡 Troubleshooting Tips:');
        $this->newLine();

        $message = $e->getMessage();

        if (str_contains($message, '401') || str_contains($message, 'Unauthorized')) {
            $this->line('• Check that your API key is correct and not expired');
            $this->line('• Ensure the API key has the necessary permissions');
            $this->line('• Verify the API key in your .env file: CANVAS_API_KEY');
        } elseif (str_contains($message, '404') || str_contains($message, 'Not Found')) {
            $this->line('• Verify the base URL is correct (including https://)');
            $this->line('• Check if the API endpoint path is correct');
            $this->line('• Ensure you\'re using the right API version (v1)');
        } elseif (str_contains($message, 'Could not resolve host') || str_contains($message, 'Connection refused')) {
            $this->line('• Check your internet connection');
            $this->line('• Verify the Canvas instance URL is correct');
            $this->line('• Ensure the Canvas instance is accessible');
        } elseif (str_contains($message, 'SSL')) {
            $this->line('• SSL certificate issue detected');
            $this->line('• For local/development Canvas instances, you may need to disable SSL verification');
            $this->line('• For production, ensure valid SSL certificates are in place');
        } else {
            $this->line('• Check your .env configuration:');
            $this->line('  - CANVAS_API_KEY');
            $this->line('  - CANVAS_BASE_URL');
            $this->line('  - CANVAS_ACCOUNT_ID');
            $this->line('• Run "php artisan config:clear" to clear cached configuration');
            $this->line('• Check the Laravel log files for more details');
        }

        $this->newLine();
        $this->line('For more help, visit: https://github.com/jjuanrivvera/canvas-lms-kit-laravel');
    }

    /**
     * Mask an API key for display.
     */
    protected function maskApiKey(string $apiKey): string
    {
        if ($apiKey === '') {
            return 'Not configured';
        }

        $length = strlen($apiKey);
        if ($length <= 8) {
            return str_repeat('*', $length);
        }

        return substr($apiKey, 0, 4) . str_repeat('*', $length - 8) . substr($apiKey, -4);
    }

    /**
     * Get a simple error type description.
     */
    protected function getErrorType(Exception $e): string
    {
        $message = $e->getMessage();

        if (str_contains($message, '401')) {
            return 'Unauthorized';
        } elseif (str_contains($message, '403')) {
            return 'Forbidden';
        } elseif (str_contains($message, '404')) {
            return 'Not Found';
        } elseif (str_contains($message, '500')) {
            return 'Server Error';
        }

        return 'Error';
    }
}
