<?php

namespace CanvasLMS\Laravel;

use CanvasLMS\Api\Accounts\Account;
use CanvasLMS\Api\Admins\Admin;
use CanvasLMS\Api\Analytics\Analytics;
use CanvasLMS\Api\Announcements\Announcement;
use CanvasLMS\Api\AppointmentGroups\AppointmentGroup;
use CanvasLMS\Api\Assignments\Assignment;
use CanvasLMS\Api\Bookmarks\Bookmark;
use CanvasLMS\Api\BrandConfigs\BrandConfig;
use CanvasLMS\Api\CalendarEvents\CalendarEvent;
use CanvasLMS\Api\Conferences\Conference;
use CanvasLMS\Api\ContentMigrations\ContentMigration;
use CanvasLMS\Api\Conversations\Conversation;
use CanvasLMS\Api\CourseReports\CourseReports;
use CanvasLMS\Api\Courses\Course;
use CanvasLMS\Api\DeveloperKeys\DeveloperKey;
use CanvasLMS\Api\DiscussionTopics\DiscussionTopic;
use CanvasLMS\Api\Enrollments\Enrollment;
use CanvasLMS\Api\ExternalTools\ExternalTool;
use CanvasLMS\Api\FeatureFlags\FeatureFlag;
use CanvasLMS\Api\Files\File;
use CanvasLMS\Api\GradebookHistory\GradebookHistory;
use CanvasLMS\Api\GroupCategories\GroupCategory;
use CanvasLMS\Api\Groups\Group;
use CanvasLMS\Api\Logins\Login;
use CanvasLMS\Api\MediaObjects\MediaObject;
use CanvasLMS\Api\Modules\Module;
use CanvasLMS\Api\OutcomeGroups\OutcomeGroup;
use CanvasLMS\Api\OutcomeImports\OutcomeImport;
use CanvasLMS\Api\OutcomeResults\OutcomeResult;
use CanvasLMS\Api\Outcomes\Outcome;
use CanvasLMS\Api\Pages\Page;
use CanvasLMS\Api\Progress\Progress;
use CanvasLMS\Api\QuizSubmissions\QuizSubmission;
use CanvasLMS\Api\Quizzes\Quiz;
use CanvasLMS\Api\Rubrics\Rubric;
use CanvasLMS\Api\Sections\Section;
use CanvasLMS\Api\SubmissionComments\SubmissionComment;
use CanvasLMS\Api\Submissions\Submission;
use CanvasLMS\Api\Tabs\Tab;
use CanvasLMS\Api\Users\User;
use CanvasLMS\Canvas;
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
 * @method class-string<\CanvasLMS\Api\Courses\Course>   courses()
 * @method \CanvasLMS\Api\Courses\Course                 course(int $id)
 * @method class-string<\CanvasLMS\Api\Users\User>       users()
 * @method \CanvasLMS\Api\Users\User                     user(int $id)
 * @method class-string<\CanvasLMS\Api\Accounts\Account> accounts()
 * @method \CanvasLMS\Api\Accounts\Account               account(int $id)
 *
 * Course Components:
 * @method class-string<\CanvasLMS\Api\Enrollments\Enrollment>     enrollments()
 * @method \CanvasLMS\Api\Enrollments\Enrollment                   enrollment(int $id)
 * @method class-string<\CanvasLMS\Api\Assignments\Assignment>     assignments()
 * @method \CanvasLMS\Api\Assignments\Assignment                   assignment(int $id)
 * @method class-string<\CanvasLMS\Api\Modules\Module>             modules()
 * @method \CanvasLMS\Api\Modules\Module                           module(int $id)
 * @method class-string<\CanvasLMS\Api\Pages\Page>                 pages()
 * @method \CanvasLMS\Api\Pages\Page                               page(int $id)
 * @method class-string<\CanvasLMS\Api\Sections\Section>           sections()
 * @method \CanvasLMS\Api\Sections\Section                         section(int $id)
 * @method class-string<\CanvasLMS\Api\Tabs\Tab>                   tabs()
 * @method \CanvasLMS\Api\Tabs\Tab                                 tab(int $id)
 * @method class-string<\CanvasLMS\Api\Announcements\Announcement> announcements()
 * @method \CanvasLMS\Api\Announcements\Announcement               announcement(int $id)
 *
 * Discussions:
 * @method class-string<\CanvasLMS\Api\DiscussionTopics\DiscussionTopic> discussionTopics()
 * @method class-string<\CanvasLMS\Api\DiscussionTopics\DiscussionTopic> discussion_topics()
 * @method \CanvasLMS\Api\DiscussionTopics\DiscussionTopic               discussionTopic(int $id)
 * @method \CanvasLMS\Api\DiscussionTopics\DiscussionTopic               discussion_topic(int $id)
 *
 * Files & Media:
 * @method class-string<\CanvasLMS\Api\Files\File>               files()
 * @method \CanvasLMS\Api\Files\File                             file(int $id)
 * @method class-string<\CanvasLMS\Api\MediaObjects\MediaObject> mediaObjects()
 * @method class-string<\CanvasLMS\Api\MediaObjects\MediaObject> media_objects()
 * @method \CanvasLMS\Api\MediaObjects\MediaObject               mediaObject(int $id)
 * @method \CanvasLMS\Api\MediaObjects\MediaObject               media_object(int $id)
 *
 * Grading & Assessment:
 * @method class-string<\CanvasLMS\Api\Quizzes\Quiz>                         quizzes()
 * @method \CanvasLMS\Api\Quizzes\Quiz                                       quiz(int $id)
 * @method class-string<\CanvasLMS\Api\QuizSubmissions\QuizSubmission>       quizSubmissions()
 * @method class-string<\CanvasLMS\Api\QuizSubmissions\QuizSubmission>       quiz_submissions()
 * @method \CanvasLMS\Api\QuizSubmissions\QuizSubmission                     quizSubmission(int $id)
 * @method \CanvasLMS\Api\QuizSubmissions\QuizSubmission                     quiz_submission(int $id)
 * @method class-string<\CanvasLMS\Api\Submissions\Submission>               submissions()
 * @method \CanvasLMS\Api\Submissions\Submission                             submission(int $id)
 * @method class-string<\CanvasLMS\Api\SubmissionComments\SubmissionComment> submissionComments()
 * @method class-string<\CanvasLMS\Api\SubmissionComments\SubmissionComment> submission_comments()
 * @method \CanvasLMS\Api\SubmissionComments\SubmissionComment               submissionComment(int $id)
 * @method \CanvasLMS\Api\SubmissionComments\SubmissionComment               submission_comment(int $id)
 * @method class-string<\CanvasLMS\Api\Rubrics\Rubric>                       rubrics()
 * @method \CanvasLMS\Api\Rubrics\Rubric                                     rubric(int $id)
 * @method class-string<\CanvasLMS\Api\GradebookHistory\GradebookHistory>    gradebookHistory()
 * @method class-string<\CanvasLMS\Api\GradebookHistory\GradebookHistory>    gradebook_history()
 *
 * Groups:
 * @method class-string<\CanvasLMS\Api\Groups\Group>                  groups()
 * @method \CanvasLMS\Api\Groups\Group                                group(int $id)
 * @method class-string<\CanvasLMS\Api\GroupCategories\GroupCategory> groupCategories()
 * @method class-string<\CanvasLMS\Api\GroupCategories\GroupCategory> group_categories()
 * @method \CanvasLMS\Api\GroupCategories\GroupCategory               groupCategory(int $id)
 * @method \CanvasLMS\Api\GroupCategories\GroupCategory               group_category(int $id)
 *
 * Outcomes:
 * @method class-string<\CanvasLMS\Api\Outcomes\Outcome>             outcomes()
 * @method \CanvasLMS\Api\Outcomes\Outcome                           outcome(int $id)
 * @method class-string<\CanvasLMS\Api\OutcomeGroups\OutcomeGroup>   outcomeGroups()
 * @method class-string<\CanvasLMS\Api\OutcomeGroups\OutcomeGroup>   outcome_groups()
 * @method \CanvasLMS\Api\OutcomeGroups\OutcomeGroup                 outcomeGroup(int $id)
 * @method \CanvasLMS\Api\OutcomeGroups\OutcomeGroup                 outcome_group(int $id)
 * @method class-string<\CanvasLMS\Api\OutcomeResults\OutcomeResult> outcomeResults()
 * @method class-string<\CanvasLMS\Api\OutcomeResults\OutcomeResult> outcome_results()
 * @method \CanvasLMS\Api\OutcomeResults\OutcomeResult               outcomeResult(int $id)
 * @method \CanvasLMS\Api\OutcomeResults\OutcomeResult               outcome_result(int $id)
 * @method class-string<\CanvasLMS\Api\OutcomeImports\OutcomeImport> outcomeImports()
 * @method class-string<\CanvasLMS\Api\OutcomeImports\OutcomeImport> outcome_imports()
 * @method \CanvasLMS\Api\OutcomeImports\OutcomeImport               outcomeImport(int $id)
 * @method \CanvasLMS\Api\OutcomeImports\OutcomeImport               outcome_import(int $id)
 *
 * Calendar & Scheduling:
 * @method class-string<\CanvasLMS\Api\CalendarEvents\CalendarEvent>       calendarEvents()
 * @method class-string<\CanvasLMS\Api\CalendarEvents\CalendarEvent>       calendar_events()
 * @method \CanvasLMS\Api\CalendarEvents\CalendarEvent                     calendarEvent(int $id)
 * @method \CanvasLMS\Api\CalendarEvents\CalendarEvent                     calendar_event(int $id)
 * @method class-string<\CanvasLMS\Api\AppointmentGroups\AppointmentGroup> appointmentGroups()
 * @method class-string<\CanvasLMS\Api\AppointmentGroups\AppointmentGroup> appointment_groups()
 * @method \CanvasLMS\Api\AppointmentGroups\AppointmentGroup               appointmentGroup(int $id)
 * @method \CanvasLMS\Api\AppointmentGroups\AppointmentGroup               appointment_group(int $id)
 *
 * Communication:
 * @method class-string<\CanvasLMS\Api\Conversations\Conversation> conversations()
 * @method \CanvasLMS\Api\Conversations\Conversation               conversation(int $id)
 * @method class-string<\CanvasLMS\Api\Conferences\Conference>     conferences()
 * @method \CanvasLMS\Api\Conferences\Conference                   conference(int $id)
 *
 * Admin & Configuration:
 * @method class-string<\CanvasLMS\Api\Admins\Admin>               admins()
 * @method \CanvasLMS\Api\Admins\Admin                             admin(int $id)
 * @method class-string<\CanvasLMS\Api\FeatureFlags\FeatureFlag>   featureFlags()
 * @method class-string<\CanvasLMS\Api\FeatureFlags\FeatureFlag>   feature_flags()
 * @method \CanvasLMS\Api\FeatureFlags\FeatureFlag                 featureFlag(int $id)
 * @method \CanvasLMS\Api\FeatureFlags\FeatureFlag                 feature_flag(int $id)
 * @method class-string<\CanvasLMS\Api\ExternalTools\ExternalTool> externalTools()
 * @method class-string<\CanvasLMS\Api\ExternalTools\ExternalTool> external_tools()
 * @method \CanvasLMS\Api\ExternalTools\ExternalTool               externalTool(int $id)
 * @method \CanvasLMS\Api\ExternalTools\ExternalTool               external_tool(int $id)
 *
 * Content & Migration:
 * @method class-string<\CanvasLMS\Api\ContentMigrations\ContentMigration> contentMigrations()
 * @method class-string<\CanvasLMS\Api\ContentMigrations\ContentMigration> content_migrations()
 * @method \CanvasLMS\Api\ContentMigrations\ContentMigration               contentMigration(int $id)
 * @method \CanvasLMS\Api\ContentMigrations\ContentMigration               content_migration(int $id)
 * @method class-string<\CanvasLMS\Api\Progress\Progress>                  progress()
 *
 * Reports & Analytics:
 * @method class-string<\CanvasLMS\Api\CourseReports\CourseReports> courseReports()
 * @method class-string<\CanvasLMS\Api\CourseReports\CourseReports> course_reports()
 * @method class-string<\CanvasLMS\Api\Analytics\Analytics>         analytics()
 *
 * Authentication & User Management:
 * @method class-string<\CanvasLMS\Api\Logins\Login> logins()
 * @method \CanvasLMS\Api\Logins\Login               login(int $id)
 *
 * Bookmarks & Favorites:
 * @method class-string<\CanvasLMS\Api\Bookmarks\Bookmark> bookmarks()
 * @method \CanvasLMS\Api\Bookmarks\Bookmark               bookmark(int $id)
 *
 * Branding & Theming:
 * @method class-string<\CanvasLMS\Api\BrandConfigs\BrandConfig> brandConfigs()
 * @method class-string<\CanvasLMS\Api\BrandConfigs\BrandConfig> brand_configs()
 * @method \CanvasLMS\Api\BrandConfigs\BrandConfig               brandConfig(int $id)
 * @method \CanvasLMS\Api\BrandConfigs\BrandConfig               brand_config(int $id)
 *
 * Developer Keys:
 * @method class-string<\CanvasLMS\Api\DeveloperKeys\DeveloperKey> developerKeys()
 * @method class-string<\CanvasLMS\Api\DeveloperKeys\DeveloperKey> developer_keys()
 * @method \CanvasLMS\Api\DeveloperKeys\DeveloperKey               developerKey(int $id)
 * @method \CanvasLMS\Api\DeveloperKeys\DeveloperKey               developer_key(int $id)
 *
 * Raw API Access:
 * @method \CanvasLMS\Canvas raw()
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
        'courses'              => Course::class,
        'course'               => Course::class,
        'users'                => User::class,
        'user'                 => User::class,
        'accounts'             => Account::class,
        'account'              => Account::class,

        // Course Components
        'enrollments'          => Enrollment::class,
        'enrollment'           => Enrollment::class,
        'assignments'          => Assignment::class,
        'assignment'           => Assignment::class,
        'modules'              => Module::class,
        'module'               => Module::class,
        'pages'                => Page::class,
        'page'                 => Page::class,
        'sections'             => Section::class,
        'section'              => Section::class,
        'tabs'                 => Tab::class,
        'tab'                  => Tab::class,
        'announcements'        => Announcement::class,
        'announcement'         => Announcement::class,

        // Discussions
        'discussionTopics'     => DiscussionTopic::class,
        'discussionTopic'      => DiscussionTopic::class,
        'discussion_topics'    => DiscussionTopic::class,
        'discussion_topic'     => DiscussionTopic::class,

        // Files & Media
        'files'                => File::class,
        'file'                 => File::class,
        'mediaObjects'         => MediaObject::class,
        'mediaObject'          => MediaObject::class,
        'media_objects'        => MediaObject::class,
        'media_object'         => MediaObject::class,

        // Grading & Assessment
        'quizzes'              => Quiz::class,
        'quiz'                 => Quiz::class,
        'quizSubmissions'      => QuizSubmission::class,
        'quizSubmission'       => QuizSubmission::class,
        'quiz_submissions'     => QuizSubmission::class,
        'quiz_submission'      => QuizSubmission::class,
        'submissions'          => Submission::class,
        'submission'           => Submission::class,
        'submissionComments'   => SubmissionComment::class,
        'submissionComment'    => SubmissionComment::class,
        'submission_comments'  => SubmissionComment::class,
        'submission_comment'   => SubmissionComment::class,
        'rubrics'              => Rubric::class,
        'rubric'               => Rubric::class,
        'gradebookHistory'     => GradebookHistory::class,
        'gradebook_history'    => GradebookHistory::class,

        // Groups
        'groups'               => Group::class,
        'group'                => Group::class,
        'groupCategories'      => GroupCategory::class,
        'groupCategory'        => GroupCategory::class,
        'group_categories'     => GroupCategory::class,
        'group_category'       => GroupCategory::class,

        // Outcomes
        'outcomes'             => Outcome::class,
        'outcome'              => Outcome::class,
        'outcomeGroups'        => OutcomeGroup::class,
        'outcomeGroup'         => OutcomeGroup::class,
        'outcome_groups'       => OutcomeGroup::class,
        'outcome_group'        => OutcomeGroup::class,
        'outcomeResults'       => OutcomeResult::class,
        'outcomeResult'        => OutcomeResult::class,
        'outcome_results'      => OutcomeResult::class,
        'outcome_result'       => OutcomeResult::class,
        'outcomeImports'       => OutcomeImport::class,
        'outcomeImport'        => OutcomeImport::class,
        'outcome_imports'      => OutcomeImport::class,
        'outcome_import'       => OutcomeImport::class,

        // Calendar & Scheduling
        'calendarEvents'       => CalendarEvent::class,
        'calendarEvent'        => CalendarEvent::class,
        'calendar_events'      => CalendarEvent::class,
        'calendar_event'       => CalendarEvent::class,
        'appointmentGroups'    => AppointmentGroup::class,
        'appointmentGroup'     => AppointmentGroup::class,
        'appointment_groups'   => AppointmentGroup::class,
        'appointment_group'    => AppointmentGroup::class,

        // Communication
        'conversations'        => Conversation::class,
        'conversation'         => Conversation::class,
        'conferences'          => Conference::class,
        'conference'           => Conference::class,

        // Admin & Configuration
        'admins'               => Admin::class,
        'admin'                => Admin::class,
        'featureFlags'         => FeatureFlag::class,
        'featureFlag'          => FeatureFlag::class,
        'feature_flags'        => FeatureFlag::class,
        'feature_flag'         => FeatureFlag::class,
        'externalTools'        => ExternalTool::class,
        'externalTool'         => ExternalTool::class,
        'external_tools'       => ExternalTool::class,
        'external_tool'        => ExternalTool::class,

        // Content & Migration
        'contentMigrations'    => ContentMigration::class,
        'contentMigration'     => ContentMigration::class,
        'content_migrations'   => ContentMigration::class,
        'content_migration'    => ContentMigration::class,
        'progress'             => Progress::class,

        // Reports & Analytics
        'courseReports'        => CourseReports::class,
        'course_reports'       => CourseReports::class,
        'analytics'            => Analytics::class,

        // Authentication & User Management
        'logins'               => Login::class,
        'login'                => Login::class,

        // Bookmarks & Favorites
        'bookmarks'            => Bookmark::class,
        'bookmark'             => Bookmark::class,

        // Branding & Theming
        'brandConfigs'         => BrandConfig::class,
        'brandConfig'          => BrandConfig::class,
        'brand_configs'        => BrandConfig::class,
        'brand_config'         => BrandConfig::class,

        // Developer Keys
        'developerKeys'        => DeveloperKey::class,
        'developerKey'         => DeveloperKey::class,
        'developer_keys'       => DeveloperKey::class,
        'developer_key'        => DeveloperKey::class,
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
     * Access the raw Canvas API facade for custom requests.
     *
     * This provides direct access to the Canvas class from the SDK,
     * allowing for custom API calls to any Canvas endpoint.
     *
     * @example Canvas::raw()->get('/api/v1/custom/endpoint')
     * @example Canvas::raw()->post('/api/v1/courses/123/custom', ['data' => 'value'])
     */
    public function raw(): Canvas
    {
        return new Canvas;
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
            // This allows Canvas::courses()::get()
            return $className;
        }

        throw new \BadMethodCallException(
            "Method [{$method}] does not exist on " . static::class . '. ' .
            'Available methods: ' . implode(', ', array_keys($classMap))
        );
    }
}
