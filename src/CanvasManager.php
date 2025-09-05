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
        $classMap = [
            // Core Resources
            'courses'              => \CanvasLMS\Api\Courses\Course::class,
            'course'               => \CanvasLMS\Api\Courses\Course::class,
            'users'                => \CanvasLMS\Api\Users\User::class,
            'user'                 => \CanvasLMS\Api\Users\User::class,
            'accounts'             => \CanvasLMS\Api\Accounts\Account::class,
            'account'              => \CanvasLMS\Api\Accounts\Account::class,

            // Course Components
            'enrollments'          => \CanvasLMS\Api\Enrollments\Enrollment::class,
            'enrollment'           => \CanvasLMS\Api\Enrollments\Enrollment::class,
            'assignments'          => \CanvasLMS\Api\Assignments\Assignment::class,
            'assignment'           => \CanvasLMS\Api\Assignments\Assignment::class,
            'modules'              => \CanvasLMS\Api\Modules\Module::class,
            'module'               => \CanvasLMS\Api\Modules\Module::class,
            'pages'                => \CanvasLMS\Api\Pages\Page::class,
            'page'                 => \CanvasLMS\Api\Pages\Page::class,
            'sections'             => \CanvasLMS\Api\Sections\Section::class,
            'section'              => \CanvasLMS\Api\Sections\Section::class,
            'tabs'                 => \CanvasLMS\Api\Tabs\Tab::class,
            'tab'                  => \CanvasLMS\Api\Tabs\Tab::class,
            'announcements'        => \CanvasLMS\Api\Announcements\Announcement::class,
            'announcement'         => \CanvasLMS\Api\Announcements\Announcement::class,

            // Discussions
            'discussionTopics'     => \CanvasLMS\Api\DiscussionTopics\DiscussionTopic::class,
            'discussionTopic'      => \CanvasLMS\Api\DiscussionTopics\DiscussionTopic::class,
            'discussion_topics'    => \CanvasLMS\Api\DiscussionTopics\DiscussionTopic::class,
            'discussion_topic'     => \CanvasLMS\Api\DiscussionTopics\DiscussionTopic::class,

            // Files & Media
            'files'                => \CanvasLMS\Api\Files\File::class,
            'file'                 => \CanvasLMS\Api\Files\File::class,
            'mediaObjects'         => \CanvasLMS\Api\MediaObjects\MediaObject::class,
            'mediaObject'          => \CanvasLMS\Api\MediaObjects\MediaObject::class,
            'media_objects'        => \CanvasLMS\Api\MediaObjects\MediaObject::class,
            'media_object'         => \CanvasLMS\Api\MediaObjects\MediaObject::class,

            // Grading & Assessment
            'quizzes'              => \CanvasLMS\Api\Quizzes\Quiz::class,
            'quiz'                 => \CanvasLMS\Api\Quizzes\Quiz::class,
            'quizSubmissions'      => \CanvasLMS\Api\QuizSubmissions\QuizSubmission::class,
            'quizSubmission'       => \CanvasLMS\Api\QuizSubmissions\QuizSubmission::class,
            'quiz_submissions'     => \CanvasLMS\Api\QuizSubmissions\QuizSubmission::class,
            'quiz_submission'      => \CanvasLMS\Api\QuizSubmissions\QuizSubmission::class,
            'submissions'          => \CanvasLMS\Api\Submissions\Submission::class,
            'submission'           => \CanvasLMS\Api\Submissions\Submission::class,
            'submissionComments'   => \CanvasLMS\Api\SubmissionComments\SubmissionComment::class,
            'submissionComment'    => \CanvasLMS\Api\SubmissionComments\SubmissionComment::class,
            'submission_comments'  => \CanvasLMS\Api\SubmissionComments\SubmissionComment::class,
            'submission_comment'   => \CanvasLMS\Api\SubmissionComments\SubmissionComment::class,
            'rubrics'              => \CanvasLMS\Api\Rubrics\Rubric::class,
            'rubric'               => \CanvasLMS\Api\Rubrics\Rubric::class,
            'gradebookHistory'     => \CanvasLMS\Api\GradebookHistory\GradebookHistory::class,
            'gradebook_history'    => \CanvasLMS\Api\GradebookHistory\GradebookHistory::class,

            // Groups
            'groups'               => \CanvasLMS\Api\Groups\Group::class,
            'group'                => \CanvasLMS\Api\Groups\Group::class,
            'groupCategories'      => \CanvasLMS\Api\GroupCategories\GroupCategory::class,
            'groupCategory'        => \CanvasLMS\Api\GroupCategories\GroupCategory::class,
            'group_categories'     => \CanvasLMS\Api\GroupCategories\GroupCategory::class,
            'group_category'       => \CanvasLMS\Api\GroupCategories\GroupCategory::class,

            // Outcomes
            'outcomes'             => \CanvasLMS\Api\Outcomes\Outcome::class,
            'outcome'              => \CanvasLMS\Api\Outcomes\Outcome::class,
            'outcomeGroups'        => \CanvasLMS\Api\OutcomeGroups\OutcomeGroup::class,
            'outcomeGroup'         => \CanvasLMS\Api\OutcomeGroups\OutcomeGroup::class,
            'outcome_groups'       => \CanvasLMS\Api\OutcomeGroups\OutcomeGroup::class,
            'outcome_group'        => \CanvasLMS\Api\OutcomeGroups\OutcomeGroup::class,
            'outcomeResults'       => \CanvasLMS\Api\OutcomeResults\OutcomeResult::class,
            'outcomeResult'        => \CanvasLMS\Api\OutcomeResults\OutcomeResult::class,
            'outcome_results'      => \CanvasLMS\Api\OutcomeResults\OutcomeResult::class,
            'outcome_result'       => \CanvasLMS\Api\OutcomeResults\OutcomeResult::class,
            'outcomeImports'       => \CanvasLMS\Api\OutcomeImports\OutcomeImport::class,
            'outcomeImport'        => \CanvasLMS\Api\OutcomeImports\OutcomeImport::class,
            'outcome_imports'      => \CanvasLMS\Api\OutcomeImports\OutcomeImport::class,
            'outcome_import'       => \CanvasLMS\Api\OutcomeImports\OutcomeImport::class,

            // Calendar & Scheduling
            'calendarEvents'       => \CanvasLMS\Api\CalendarEvents\CalendarEvent::class,
            'calendarEvent'        => \CanvasLMS\Api\CalendarEvents\CalendarEvent::class,
            'calendar_events'      => \CanvasLMS\Api\CalendarEvents\CalendarEvent::class,
            'calendar_event'       => \CanvasLMS\Api\CalendarEvents\CalendarEvent::class,
            'appointmentGroups'    => \CanvasLMS\Api\AppointmentGroups\AppointmentGroup::class,
            'appointmentGroup'     => \CanvasLMS\Api\AppointmentGroups\AppointmentGroup::class,
            'appointment_groups'   => \CanvasLMS\Api\AppointmentGroups\AppointmentGroup::class,
            'appointment_group'    => \CanvasLMS\Api\AppointmentGroups\AppointmentGroup::class,

            // Communication
            'conversations'        => \CanvasLMS\Api\Conversations\Conversation::class,
            'conversation'         => \CanvasLMS\Api\Conversations\Conversation::class,
            'conferences'          => \CanvasLMS\Api\Conferences\Conference::class,
            'conference'           => \CanvasLMS\Api\Conferences\Conference::class,

            // Admin & Configuration
            'admins'               => \CanvasLMS\Api\Admins\Admin::class,
            'admin'                => \CanvasLMS\Api\Admins\Admin::class,
            'featureFlags'         => \CanvasLMS\Api\FeatureFlags\FeatureFlag::class,
            'featureFlag'          => \CanvasLMS\Api\FeatureFlags\FeatureFlag::class,
            'feature_flags'        => \CanvasLMS\Api\FeatureFlags\FeatureFlag::class,
            'feature_flag'         => \CanvasLMS\Api\FeatureFlags\FeatureFlag::class,
            'externalTools'        => \CanvasLMS\Api\ExternalTools\ExternalTool::class,
            'externalTool'         => \CanvasLMS\Api\ExternalTools\ExternalTool::class,
            'external_tools'       => \CanvasLMS\Api\ExternalTools\ExternalTool::class,
            'external_tool'        => \CanvasLMS\Api\ExternalTools\ExternalTool::class,

            // Content & Migration
            'contentMigrations'    => \CanvasLMS\Api\ContentMigrations\ContentMigration::class,
            'contentMigration'     => \CanvasLMS\Api\ContentMigrations\ContentMigration::class,
            'content_migrations'   => \CanvasLMS\Api\ContentMigrations\ContentMigration::class,
            'content_migration'    => \CanvasLMS\Api\ContentMigrations\ContentMigration::class,
            'progress'             => \CanvasLMS\Api\Progress\Progress::class,
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
