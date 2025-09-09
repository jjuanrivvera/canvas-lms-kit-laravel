<?php

namespace CanvasLMS\Laravel\Concerns;

use CanvasLMS\Config;
use Illuminate\Support\Facades\Log;

trait ConfiguresCanvas
{
    /**
     * Apply Canvas LMS Kit configuration from a config array.
     *
     * @param array<string, mixed> $config
     */
    protected function applyCanvasConfiguration(array $config): void
    {
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
            } catch (\Exception) {
                // Silently fail if log channel doesn't exist
                // This prevents breaking the application during config caching
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

        // Configure authentication based on auth_mode
        $authMode = $config['auth_mode'] ?? 'api_key';

        if ($authMode === 'oauth') {
            // Set OAuth credentials if using OAuth mode
            if (isset($config['oauth_client_id']) && $config['oauth_client_id'] !== '') {
                Config::setOAuthClientId($config['oauth_client_id']);
            }

            if (isset($config['oauth_client_secret']) && $config['oauth_client_secret'] !== '') {
                Config::setOAuthClientSecret($config['oauth_client_secret']);
            }

            if (isset($config['oauth_redirect_uri']) && $config['oauth_redirect_uri'] !== '') {
                Config::setOAuthRedirectUri($config['oauth_redirect_uri']);
            }

            if (isset($config['oauth_token']) && $config['oauth_token'] !== '') {
                Config::setOAuthToken($config['oauth_token']);
            }

            if (isset($config['oauth_refresh_token']) && $config['oauth_refresh_token'] !== '') {
                Config::setOAuthRefreshToken($config['oauth_refresh_token']);
            }

            // Switch to OAuth mode
            Config::useOAuth();
        } else {
            // Ensure API key mode is active (default)
            Config::useApiKey();
        }
    }
}
