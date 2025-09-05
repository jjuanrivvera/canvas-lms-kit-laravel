<?php

namespace CanvasLMS\Laravel;

use CanvasLMS\Config;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Canvas Manager for handling multiple Canvas connections.
 *
 * This class manages switching between different Canvas LMS instances
 * in multi-tenant applications or when working with multiple Canvas
 * environments (production, sandbox, etc.).
 */
class CanvasManager
{
    /**
     * The Canvas configuration array.
     *
     * @var array<string, mixed>
     */
    protected array $config;

    /**
     * The currently active connection name.
     */
    protected string $currentConnection;

    /**
     * Create a new Canvas Manager instance.
     *
     * @param array<string, mixed> $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->currentConnection = $config['default'] ?? 'main';
    }

    /**
     * Switch to a different Canvas connection.
     *
     * @param string $name The connection name
     *
     * @throws InvalidArgumentException If the connection doesn't exist
     */
    public function connection(string $name): self
    {
        if (! isset($this->config['connections'][$name])) {
            throw new InvalidArgumentException(
                "Canvas connection [{$name}] is not configured. " .
                'Available connections: ' . implode(', ', array_keys($this->config['connections'] ?? []))
            );
        }

        $this->configureConnection($name);
        $this->currentConnection = $name;

        return $this;
    }

    /**
     * Configure Canvas LMS Kit with a specific connection's settings.
     *
     * @param string $name The connection name
     */
    protected function configureConnection(string $name): void
    {
        $config = $this->config['connections'][$name];

        // Set API credentials
        if (isset($config['api_key']) && $config['api_key'] !== '') {
            Config::setApiKey($config['api_key']);
        }

        if (isset($config['base_url']) && $config['base_url'] !== '') {
            Config::setBaseUrl($config['base_url']);
        }

        // Set optional configuration
        if (isset($config['account_id'])) {
            Config::setAccountId($config['account_id']);
        }

        if (isset($config['timeout'])) {
            Config::setTimeout($config['timeout']);
        }

        // Configure logging
        if (isset($config['log_channel']) && $config['log_channel'] !== '') {
            try {
                $logger = Log::channel($config['log_channel']);
                Config::setLogger($logger);
            } catch (\Exception $e) {
                // Silently fail if log channel doesn't exist
            }
        }

        // Set API version if configured
        if (isset($config['api_version']) && $config['api_version'] !== '') {
            Config::setApiVersion($config['api_version']);
        }

        // Configure middleware if specified
        if (isset($config['middleware']) && is_array($config['middleware'])) {
            Config::setMiddleware($config['middleware']);
        }
    }

    /**
     * Get the current connection name.
     */
    public function getConnection(): string
    {
        return $this->currentConnection;
    }

    /**
     * Get the configuration for the current connection.
     *
     * @return array<string, mixed>|null
     */
    public function getConnectionConfig(): ?array
    {
        return $this->config['connections'][$this->currentConnection] ?? null;
    }

    /**
     * Get all available connection names.
     *
     * @return array<string>
     */
    public function getAvailableConnections(): array
    {
        $connections = $this->config['connections'] ?? [];
        /** @var array<string> */
        $keys = array_keys($connections);

        return $keys;
    }

    /**
     * Execute a callback using a specific connection.
     *
     * @param string   $connection The connection name
     * @param callable $callback   The callback to execute
     *
     * @return mixed The callback's return value
     */
    public function usingConnection(string $connection, callable $callback): mixed
    {
        $previousConnection = $this->currentConnection;

        try {
            $this->connection($connection);

            return $callback($this);
        } finally {
            // Restore the previous connection
            $this->connection($previousConnection);
        }
    }

    /**
     * Dynamically proxy method calls to Canvas LMS Kit API classes.
     *
     * This allows for syntax like:
     * - Canvas::courses() to access Course API
     * - Canvas::users() to access User API
     * - Canvas::enrollments() to access Enrollment API
     *
     * @param array<mixed> $parameters
     *
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        // Map method names to Canvas API classes
        $classMap = [
            'courses'       => \CanvasLMS\Api\Courses\Course::class,
            'course'        => \CanvasLMS\Api\Courses\Course::class,
            'users'         => \CanvasLMS\Api\Users\User::class,
            'user'          => \CanvasLMS\Api\Users\User::class,
            'enrollments'   => \CanvasLMS\Api\Enrollments\Enrollment::class,
            'enrollment'    => \CanvasLMS\Api\Enrollments\Enrollment::class,
            'assignments'   => \CanvasLMS\Api\Assignments\Assignment::class,
            'assignment'    => \CanvasLMS\Api\Assignments\Assignment::class,
            'modules'       => \CanvasLMS\Api\Modules\Module::class,
            'module'        => \CanvasLMS\Api\Modules\Module::class,
            'pages'         => \CanvasLMS\Api\Pages\Page::class,
            'page'          => \CanvasLMS\Api\Pages\Page::class,
            'files'         => \CanvasLMS\Api\Files\File::class,
            'file'          => \CanvasLMS\Api\Files\File::class,
            'folders'       => \CanvasLMS\Api\Folders\Folder::class,
            'folder'        => \CanvasLMS\Api\Folders\Folder::class,
            'groups'        => \CanvasLMS\Api\Groups\Group::class,
            'group'         => \CanvasLMS\Api\Groups\Group::class,
            'sections'      => \CanvasLMS\Api\Sections\Section::class,
            'section'       => \CanvasLMS\Api\Sections\Section::class,
            'accounts'      => \CanvasLMS\Api\Accounts\Account::class,
            'account'       => \CanvasLMS\Api\Accounts\Account::class,
            'roles'         => \CanvasLMS\Api\Roles\Role::class,
            'role'          => \CanvasLMS\Api\Roles\Role::class,
            'admins'        => \CanvasLMS\Api\Admins\Admin::class,
            'admin'         => \CanvasLMS\Api\Admins\Admin::class,
            'analytics'     => \CanvasLMS\Api\Analytics\Analytics::class,
            'conversations' => \CanvasLMS\Api\Conversations\Conversation::class,
            'conversation'  => \CanvasLMS\Api\Conversations\Conversation::class,
        ];

        $methodLower = strtolower($method);

        if (isset($classMap[$methodLower])) {
            $className = $classMap[$methodLower];

            // If parameters are provided and it's a singular method (like 'course'),
            // assume they want to find by ID
            if (count($parameters) > 0 && ! str_ends_with($methodLower, 's')) {
                return $className::find(...$parameters);
            }

            // Return the class name for static method chaining
            // This allows Canvas::courses()::fetchAll()
            return $className;
        }

        throw new \BadMethodCallException(
            "Method [{$method}] does not exist on " . static::class . '. ' .
            'Available methods: ' . implode(', ', array_keys($classMap))
        );
    }
}
