<?php

namespace CanvasLMS\Laravel\Validation;

use InvalidArgumentException;

class ConfigurationValidator
{
    /**
     * Cache for validation results to improve performance.
     *
     * @var array<string, bool>
     */
    private static array $validationCache = [];

    /**
     * Compiled regex patterns cache to improve performance.
     *
     * @var array<string, string>
     */
    private static array $regexCache = [];

    /**
     * Validate Canvas configuration with caching and performance optimizations.
     *
     * @param array<string, mixed> $config
     *
     * @throws InvalidArgumentException
     */
    public static function validateCanvasConfigurationOptimized(array $config, bool $useCache = true): void
    {
        if ($useCache) {
            $cacheKey = md5(serialize($config));

            if (isset(self::$validationCache[$cacheKey])) {
                return;
            }
        }

        self::validateCanvasConfiguration($config);

        if ($useCache) {
            self::$validationCache[$cacheKey] = true;
        }
    }

    /**
     * Clear the validation cache.
     */
    public static function clearValidationCache(): void
    {
        self::$validationCache = [];
        self::$regexCache = [];
    }

    /**
     * Get a compiled regex pattern with caching.
     */
    private static function getCompiledRegex(string $pattern): string
    {
        if (! isset(self::$regexCache[$pattern])) {
            self::$regexCache[$pattern] = $pattern;
        }

        return self::$regexCache[$pattern];
    }

    /**
     * Validate a Canvas connection configuration.
     *
     * @param array<string, mixed> $config
     *
     * @throws InvalidArgumentException
     */
    public static function validateConnection(array $config, string $connectionName = 'default'): void
    {
        // Validate authentication mode
        $authMode = $config['auth_mode'] ?? 'api_key';
        self::validateAuthMode($authMode, $connectionName);

        // Validate based on authentication mode
        if ($authMode === 'oauth') {
            self::validateOAuthConfiguration($config, $connectionName);
        } else {
            self::validateApiKeyConfiguration($config, $connectionName);
        }

        // Validate common configuration
        self::validateBaseUrl($config, $connectionName);
        self::validateAccountId($config, $connectionName);
        self::validateTimeout($config, $connectionName);
        self::validateApiVersion($config, $connectionName);
    }

    /**
     * Validate authentication mode.
     *
     * @throws InvalidArgumentException
     */
    private static function validateAuthMode(mixed $authMode, string $connectionName): void
    {
        if (! is_string($authMode)) {
            throw new InvalidArgumentException(
                "Canvas connection [{$connectionName}]: auth_mode must be a string, " . gettype($authMode) . ' given.'
            );
        }

        $validModes = ['api_key', 'oauth'];
        if (! in_array($authMode, $validModes, true)) {
            throw new InvalidArgumentException(
                "Canvas connection [{$connectionName}]: auth_mode must be either 'api_key' or 'oauth', '{$authMode}' given."
            );
        }
    }

    /**
     * Validate API key configuration.
     *
     * @param array<string, mixed> $config
     *
     * @throws InvalidArgumentException
     */
    private static function validateApiKeyConfiguration(array $config, string $connectionName): void
    {
        $apiKey = $config['api_key'] ?? null;

        if ($apiKey === null || $apiKey === '') {
            throw new InvalidArgumentException(
                "Canvas connection [{$connectionName}]: api_key is required when using 'api_key' authentication mode. " .
                'Please set the CANVAS_API_KEY environment variable or configure it directly in the canvas.php config file.'
            );
        }

        if (! is_string($apiKey)) {
            throw new InvalidArgumentException(
                "Canvas connection [{$connectionName}]: api_key must be a string, " . gettype($apiKey) . ' given.'
            );
        }

        self::validateApiKeyFormat($apiKey, $connectionName);
    }

    /**
     * Validate API key format (Canvas API keys are typically long alphanumeric strings).
     *
     * @throws InvalidArgumentException
     */
    private static function validateApiKeyFormat(string $apiKey, string $connectionName): void
    {
        // Canvas API keys are typically long alphanumeric strings with underscores
        // They're usually 70+ characters long and contain alphanumeric characters and underscores
        if (strlen($apiKey) < 20) {
            throw new InvalidArgumentException(
                "Canvas connection [{$connectionName}]: api_key appears to be too short. " .
                'Canvas API keys are typically long alphanumeric strings. ' .
                'Please verify your API key is correct.'
            );
        }

        if (preg_match(self::getCompiledRegex('/^[a-zA-Z0-9_-]+$/'), $apiKey) !== 1) {
            throw new InvalidArgumentException(
                "Canvas connection [{$connectionName}]: api_key contains invalid characters. " .
                'Canvas API keys should only contain alphanumeric characters, underscores, and hyphens.'
            );
        }
    }

    /**
     * Validate OAuth configuration.
     *
     * @param array<string, mixed> $config
     *
     * @throws InvalidArgumentException
     */
    private static function validateOAuthConfiguration(array $config, string $connectionName): void
    {
        $requiredOAuthFields = [
            'oauth_client_id'     => 'CANVAS_OAUTH_CLIENT_ID',
            'oauth_client_secret' => 'CANVAS_OAUTH_CLIENT_SECRET',
            'oauth_redirect_uri'  => 'CANVAS_OAUTH_REDIRECT_URI',
        ];

        foreach ($requiredOAuthFields as $field => $envVar) {
            if (! isset($config[$field]) || $config[$field] === '') {
                throw new InvalidArgumentException(
                    "Canvas connection [{$connectionName}]: {$field} is required when using 'oauth' authentication mode. " .
                    "Please set the {$envVar} environment variable or configure it directly in the canvas.php config file."
                );
            }

            if (! is_string($config[$field])) {
                throw new InvalidArgumentException(
                    "Canvas connection [{$connectionName}]: {$field} must be a string, " . gettype($config[$field]) . ' given.'
                );
            }
        }

        // Validate redirect URI format
        if (isset($config['oauth_redirect_uri']) && filter_var($config['oauth_redirect_uri'], FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException(
                "Canvas connection [{$connectionName}]: oauth_redirect_uri must be a valid URL."
            );
        }

        // Validate redirect URI uses HTTPS (unless localhost for development)
        if (isset($config['oauth_redirect_uri'])) {
            $redirectUri = $config['oauth_redirect_uri'];
            if (! str_starts_with($redirectUri, 'https://') && ! str_starts_with($redirectUri, 'http://localhost')) {
                throw new InvalidArgumentException(
                    "Canvas connection [{$connectionName}]: oauth_redirect_uri must use HTTPS or be localhost for development. " .
                    'Using HTTP in production is insecure.'
                );
            }
        }
    }

    /**
     * Validate base URL configuration.
     *
     * @param array<string, mixed> $config
     *
     * @throws InvalidArgumentException
     */
    private static function validateBaseUrl(array $config, string $connectionName): void
    {
        $baseUrl = $config['base_url'] ?? null;

        if ($baseUrl === null || $baseUrl === '') {
            throw new InvalidArgumentException(
                "Canvas connection [{$connectionName}]: base_url is required. " .
                'Please set the CANVAS_BASE_URL environment variable or configure it directly in the canvas.php config file.'
            );
        }

        if (! is_string($baseUrl)) {
            throw new InvalidArgumentException(
                "Canvas connection [{$connectionName}]: base_url must be a string, " . gettype($baseUrl) . ' given.'
            );
        }

        if (filter_var($baseUrl, FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException(
                "Canvas connection [{$connectionName}]: base_url must be a valid URL. '{$baseUrl}' given."
            );
        }

        // Enforce HTTPS for security (unless localhost for development)
        if (! str_starts_with($baseUrl, 'https://') && ! str_starts_with($baseUrl, 'http://localhost')) {
            throw new InvalidArgumentException(
                "Canvas connection [{$connectionName}]: base_url must use HTTPS for security. " .
                'HTTP connections to Canvas LMS instances are not secure. ' .
                'Use HTTPS or localhost for development only.'
            );
        }

        // Validate that it looks like a Canvas URL
        if (preg_match(self::getCompiledRegex('/\.(instructure\.com|canvaslms\.com)/'), $baseUrl) !== 1 &&
            ! str_contains($baseUrl, 'canvas') &&
            ! str_contains($baseUrl, 'localhost')) {
            throw new InvalidArgumentException(
                "Canvas connection [{$connectionName}]: base_url does not appear to be a valid Canvas LMS URL. " .
                "Ensure you're using the correct Canvas instance URL (e.g., https://your-school.instructure.com)."
            );
        }
    }

    /**
     * Validate account ID configuration.
     *
     * @param array<string, mixed> $config
     *
     * @throws InvalidArgumentException
     */
    private static function validateAccountId(array $config, string $connectionName): void
    {
        if (! isset($config['account_id'])) {
            return; // Optional field
        }

        $accountId = $config['account_id'];

        if (! is_int($accountId) && ! is_numeric($accountId)) {
            throw new InvalidArgumentException(
                "Canvas connection [{$connectionName}]: account_id must be a positive integer, " . gettype($accountId) . ' given.'
            );
        }

        $accountIdInt = (int) $accountId;
        if ($accountIdInt < 1) {
            throw new InvalidArgumentException(
                "Canvas connection [{$connectionName}]: account_id must be a positive integer greater than 0, {$accountIdInt} given."
            );
        }
    }

    /**
     * Validate timeout configuration.
     *
     * @param array<string, mixed> $config
     *
     * @throws InvalidArgumentException
     */
    private static function validateTimeout(array $config, string $connectionName): void
    {
        if (! isset($config['timeout'])) {
            return; // Optional field
        }

        $timeout = $config['timeout'];

        if (! is_int($timeout) && ! is_numeric($timeout)) {
            throw new InvalidArgumentException(
                "Canvas connection [{$connectionName}]: timeout must be an integer, " . gettype($timeout) . ' given.'
            );
        }

        $timeoutInt = (int) $timeout;
        if ($timeoutInt < 1 || $timeoutInt > 300) {
            throw new InvalidArgumentException(
                "Canvas connection [{$connectionName}]: timeout must be between 1 and 300 seconds, {$timeoutInt} given. " .
                'Consider using a reasonable timeout (e.g., 30 seconds) to prevent hanging requests.'
            );
        }
    }

    /**
     * Validate API version configuration.
     *
     * @param array<string, mixed> $config
     *
     * @throws InvalidArgumentException
     */
    private static function validateApiVersion(array $config, string $connectionName): void
    {
        if (! isset($config['api_version'])) {
            return; // Optional field
        }

        $apiVersion = $config['api_version'];

        if (! is_string($apiVersion)) {
            throw new InvalidArgumentException(
                "Canvas connection [{$connectionName}]: api_version must be a string, " . gettype($apiVersion) . ' given.'
            );
        }

        // Validate API version format (e.g., 'v1', 'v2', 'beta')
        if (preg_match(self::getCompiledRegex('/^(v\d+|beta)$/'), $apiVersion) !== 1) {
            throw new InvalidArgumentException(
                "Canvas connection [{$connectionName}]: api_version must be in format 'v1', 'v2', or 'beta', '{$apiVersion}' given."
            );
        }
    }

    /**
     * Validate the main Canvas configuration structure.
     *
     * @param array<string, mixed> $config
     *
     * @throws InvalidArgumentException
     */
    public static function validateCanvasConfiguration(array $config): void
    {
        // Validate default connection exists
        $defaultConnection = $config['default'] ?? null;

        if ($defaultConnection === null || $defaultConnection === '') {
            throw new InvalidArgumentException(
                "Canvas configuration: 'default' connection name is required. " .
                'Please specify which connection to use as the default.'
            );
        }

        if (! is_string($defaultConnection)) {
            throw new InvalidArgumentException(
                "Canvas configuration: 'default' must be a string, " . gettype($defaultConnection) . ' given.'
            );
        }

        // Validate connections array exists
        if (! isset($config['connections']) || ! is_array($config['connections'])) {
            throw new InvalidArgumentException(
                "Canvas configuration: 'connections' must be an array of connection configurations."
            );
        }

        if (count($config['connections']) === 0) {
            throw new InvalidArgumentException(
                "Canvas configuration: at least one connection must be configured in 'connections' array."
            );
        }

        // Validate default connection exists in connections
        if (! isset($config['connections'][$defaultConnection])) {
            $availableConnections = implode(', ', array_keys($config['connections']));
            throw new InvalidArgumentException(
                "Canvas configuration: default connection '{$defaultConnection}' is not configured. " .
                "Available connections: {$availableConnections}"
            );
        }

        // Validate each connection
        foreach ($config['connections'] as $name => $connectionConfig) {
            if (! is_array($connectionConfig)) {
                throw new InvalidArgumentException(
                    "Canvas configuration: connection '{$name}' must be an array of configuration options."
                );
            }

            self::validateConnection($connectionConfig, $name);
        }
    }
}
