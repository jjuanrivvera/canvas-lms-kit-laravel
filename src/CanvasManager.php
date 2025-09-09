<?php

namespace CanvasLMS\Laravel;

use CanvasLMS\Laravel\Concerns\ConfiguresCanvas;
use CanvasLMS\Laravel\Contracts\CanvasManagerInterface;
use InvalidArgumentException;

/**
 * Canvas Manager for handling multiple Canvas connections.
 *
 * This class manages switching between different Canvas LMS instances
 * in multi-tenant applications or when working with multiple Canvas
 * environments (production, sandbox, etc.).
 *
 * Core Resources:
 *
 * @method string                          courses()
 * @method \CanvasLMS\Api\Courses\Course   course(int $id)
 * @method string                          users()
 * @method \CanvasLMS\Api\Users\User       user(int $id)
 * @method string                          accounts()
 * @method \CanvasLMS\Api\Accounts\Account account(int $id)
 *
 * Course Components:
 * @method string                                    enrollments()
 * @method \CanvasLMS\Api\Enrollments\Enrollment     enrollment(int $id)
 * @method string                                    assignments()
 * @method \CanvasLMS\Api\Assignments\Assignment     assignment(int $id)
 * @method string                                    modules()
 * @method \CanvasLMS\Api\Modules\Module             module(int $id)
 * @method string                                    pages()
 * @method \CanvasLMS\Api\Pages\Page                 page(int $id)
 * @method string                                    sections()
 * @method \CanvasLMS\Api\Sections\Section           section(int $id)
 * @method string                                    tabs()
 * @method \CanvasLMS\Api\Tabs\Tab                   tab(int $id)
 * @method string                                    announcements()
 * @method \CanvasLMS\Api\Announcements\Announcement announcement(int $id)
 *
 * Discussions:
 * @method string                                          discussionTopics()
 * @method string                                          discussion_topics()
 * @method \CanvasLMS\Api\DiscussionTopics\DiscussionTopic discussionTopic(int $id)
 * @method \CanvasLMS\Api\DiscussionTopics\DiscussionTopic discussion_topic(int $id)
 *
 * Files & Media:
 * @method string                                  files()
 * @method \CanvasLMS\Api\Files\File               file(int $id)
 * @method string                                  mediaObjects()
 * @method string                                  media_objects()
 * @method \CanvasLMS\Api\MediaObjects\MediaObject mediaObject(int $id)
 * @method \CanvasLMS\Api\MediaObjects\MediaObject media_object(int $id)
 *
 * Grading & Assessment:
 * @method string                                              quizzes()
 * @method \CanvasLMS\Api\Quizzes\Quiz                         quiz(int $id)
 * @method string                                              quizSubmissions()
 * @method string                                              quiz_submissions()
 * @method \CanvasLMS\Api\QuizSubmissions\QuizSubmission       quizSubmission(int $id)
 * @method \CanvasLMS\Api\QuizSubmissions\QuizSubmission       quiz_submission(int $id)
 * @method string                                              submissions()
 * @method \CanvasLMS\Api\Submissions\Submission               submission(int $id)
 * @method string                                              submissionComments()
 * @method string                                              submission_comments()
 * @method \CanvasLMS\Api\SubmissionComments\SubmissionComment submissionComment(int $id)
 * @method \CanvasLMS\Api\SubmissionComments\SubmissionComment submission_comment(int $id)
 * @method string                                              rubrics()
 * @method \CanvasLMS\Api\Rubrics\Rubric                       rubric(int $id)
 * @method string                                              gradebookHistory()
 * @method string                                              gradebook_history()
 *
 * Groups:
 * @method string                                       groups()
 * @method \CanvasLMS\Api\Groups\Group                  group(int $id)
 * @method string                                       groupCategories()
 * @method string                                       group_categories()
 * @method \CanvasLMS\Api\GroupCategories\GroupCategory groupCategory(int $id)
 * @method \CanvasLMS\Api\GroupCategories\GroupCategory group_category(int $id)
 *
 * Outcomes:
 * @method string                                      outcomes()
 * @method \CanvasLMS\Api\Outcomes\Outcome             outcome(int $id)
 * @method string                                      outcomeGroups()
 * @method string                                      outcome_groups()
 * @method \CanvasLMS\Api\OutcomeGroups\OutcomeGroup   outcomeGroup(int $id)
 * @method \CanvasLMS\Api\OutcomeGroups\OutcomeGroup   outcome_group(int $id)
 * @method string                                      outcomeResults()
 * @method string                                      outcome_results()
 * @method \CanvasLMS\Api\OutcomeResults\OutcomeResult outcomeResult(int $id)
 * @method \CanvasLMS\Api\OutcomeResults\OutcomeResult outcome_result(int $id)
 * @method string                                      outcomeImports()
 * @method string                                      outcome_imports()
 * @method \CanvasLMS\Api\OutcomeImports\OutcomeImport outcomeImport(int $id)
 * @method \CanvasLMS\Api\OutcomeImports\OutcomeImport outcome_import(int $id)
 *
 * Calendar & Scheduling:
 * @method string                                            calendarEvents()
 * @method string                                            calendar_events()
 * @method \CanvasLMS\Api\CalendarEvents\CalendarEvent       calendarEvent(int $id)
 * @method \CanvasLMS\Api\CalendarEvents\CalendarEvent       calendar_event(int $id)
 * @method string                                            appointmentGroups()
 * @method string                                            appointment_groups()
 * @method \CanvasLMS\Api\AppointmentGroups\AppointmentGroup appointmentGroup(int $id)
 * @method \CanvasLMS\Api\AppointmentGroups\AppointmentGroup appointment_group(int $id)
 *
 * Communication:
 * @method string                                    conversations()
 * @method \CanvasLMS\Api\Conversations\Conversation conversation(int $id)
 * @method string                                    conferences()
 * @method \CanvasLMS\Api\Conferences\Conference     conference(int $id)
 *
 * Admin & Configuration:
 * @method string                                    admins()
 * @method \CanvasLMS\Api\Admins\Admin               admin(int $id)
 * @method string                                    featureFlags()
 * @method string                                    feature_flags()
 * @method \CanvasLMS\Api\FeatureFlags\FeatureFlag   featureFlag(int $id)
 * @method \CanvasLMS\Api\FeatureFlags\FeatureFlag   feature_flag(int $id)
 * @method string                                    externalTools()
 * @method string                                    external_tools()
 * @method \CanvasLMS\Api\ExternalTools\ExternalTool externalTool(int $id)
 * @method \CanvasLMS\Api\ExternalTools\ExternalTool external_tool(int $id)
 *
 * Content & Migration:
 * @method string                                            contentMigrations()
 * @method string                                            content_migrations()
 * @method \CanvasLMS\Api\ContentMigrations\ContentMigration contentMigration(int $id)
 * @method \CanvasLMS\Api\ContentMigrations\ContentMigration content_migration(int $id)
 * @method string                                            progress()
 */
class CanvasManager implements CanvasManagerInterface
{
    use ConfiguresCanvas;
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
     * Map of method names to Canvas API classes.
     *
     * This constant maps method names (both camelCase and snake_case)
     * to their corresponding Canvas LMS Kit API class names.
     *
     * @var array<string, class-string>
     */
    private const API_CLASS_MAP = [
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

    /**
     * Cached lowercase version of the class map for performance.
     *
     * @var array<string, class-string>|null
     */
    private static ?array $lowercaseClassMap = null;

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
        $this->applyCanvasConfiguration($config);
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
     * Get the lowercase version of the class map with caching for performance.
     *
     * @return array<string, class-string>
     */
    private function getLowercaseClassMap(): array
    {
        // Cache the lowercase class map after first use to avoid repeated array_change_key_case calls
        if (self::$lowercaseClassMap === null) {
            self::$lowercaseClassMap = array_change_key_case(self::API_CLASS_MAP, CASE_LOWER);
        }

        return self::$lowercaseClassMap;
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
        $classMap = $this->getLowercaseClassMap();
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
