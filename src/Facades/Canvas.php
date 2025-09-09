<?php

namespace CanvasLMS\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Canvas Facade for Laravel.
 *
 * @mixin \CanvasLMS\Laravel\CanvasManager
 *
 * NOTE: Direct SDK usage is recommended over this facade for cleaner, more testable code.
 *
 * @deprecated Use direct SDK classes instead (e.g., Course::get() instead of Canvas::courses()::get())
 *
 * Connection Management:
 *
 * @method static \CanvasLMS\Laravel\CanvasManager connection(string $name)
 * @method static string                           getConnection()
 * @method static array|null                       getConnectionConfig()
 * @method static array<string>                    getAvailableConnections()
 * @method static mixed                            usingConnection(string $connection, callable $callback)
 *
 * Core Resources:
 * @method static string                          courses()
 * @method static \CanvasLMS\Api\Courses\Course   course(int $id)
 * @method static string                          users()
 * @method static \CanvasLMS\Api\Users\User       user(int $id)
 * @method static string                          accounts()
 * @method static \CanvasLMS\Api\Accounts\Account account(int $id)
 *
 * Course Components:
 * @method static string                                    enrollments()
 * @method static \CanvasLMS\Api\Enrollments\Enrollment     enrollment(int $id)
 * @method static string                                    assignments()
 * @method static \CanvasLMS\Api\Assignments\Assignment     assignment(int $id)
 * @method static string                                    modules()
 * @method static \CanvasLMS\Api\Modules\Module             module(int $id)
 * @method static string                                    pages()
 * @method static \CanvasLMS\Api\Pages\Page                 page(int $id)
 * @method static string                                    sections()
 * @method static \CanvasLMS\Api\Sections\Section           section(int $id)
 * @method static string                                    tabs()
 * @method static \CanvasLMS\Api\Tabs\Tab                   tab(int $id)
 * @method static string                                    announcements()
 * @method static \CanvasLMS\Api\Announcements\Announcement announcement(int $id)
 *
 * Discussions & Communication:
 * @method static string                                          discussionTopics()
 * @method static string                                          discussion_topics()
 * @method static \CanvasLMS\Api\DiscussionTopics\DiscussionTopic discussionTopic(int $id)
 * @method static \CanvasLMS\Api\DiscussionTopics\DiscussionTopic discussion_topic(int $id)
 * @method static string                                          conversations()
 * @method static \CanvasLMS\Api\Conversations\Conversation       conversation(int $id)
 * @method static string                                          conferences()
 * @method static \CanvasLMS\Api\Conferences\Conference           conference(int $id)
 *
 * Files & Media:
 * @method static string                                  files()
 * @method static \CanvasLMS\Api\Files\File               file(int $id)
 * @method static string                                  mediaObjects()
 * @method static string                                  media_objects()
 * @method static \CanvasLMS\Api\MediaObjects\MediaObject mediaObject(int $id)
 * @method static \CanvasLMS\Api\MediaObjects\MediaObject media_object(int $id)
 *
 * Grading & Assessment:
 * @method static string                                              quizzes()
 * @method static \CanvasLMS\Api\Quizzes\Quiz                         quiz(int $id)
 * @method static string                                              quizSubmissions()
 * @method static string                                              quiz_submissions()
 * @method static \CanvasLMS\Api\QuizSubmissions\QuizSubmission       quizSubmission(int $id)
 * @method static \CanvasLMS\Api\QuizSubmissions\QuizSubmission       quiz_submission(int $id)
 * @method static string                                              submissions()
 * @method static \CanvasLMS\Api\Submissions\Submission               submission(int $id)
 * @method static string                                              submissionComments()
 * @method static string                                              submission_comments()
 * @method static \CanvasLMS\Api\SubmissionComments\SubmissionComment submissionComment(int $id)
 * @method static \CanvasLMS\Api\SubmissionComments\SubmissionComment submission_comment(int $id)
 * @method static string                                              rubrics()
 * @method static \CanvasLMS\Api\Rubrics\Rubric                       rubric(int $id)
 * @method static string                                              gradebookHistory()
 * @method static string                                              gradebook_history()
 *
 * Groups & Collaboration:
 * @method static string                                       groups()
 * @method static \CanvasLMS\Api\Groups\Group                  group(int $id)
 * @method static string                                       groupCategories()
 * @method static string                                       group_categories()
 * @method static \CanvasLMS\Api\GroupCategories\GroupCategory groupCategory(int $id)
 * @method static \CanvasLMS\Api\GroupCategories\GroupCategory group_category(int $id)
 *
 * Outcomes & Standards:
 * @method static string                                      outcomes()
 * @method static \CanvasLMS\Api\Outcomes\Outcome             outcome(int $id)
 * @method static string                                      outcomeGroups()
 * @method static string                                      outcome_groups()
 * @method static \CanvasLMS\Api\OutcomeGroups\OutcomeGroup   outcomeGroup(int $id)
 * @method static \CanvasLMS\Api\OutcomeGroups\OutcomeGroup   outcome_group(int $id)
 * @method static string                                      outcomeResults()
 * @method static string                                      outcome_results()
 * @method static \CanvasLMS\Api\OutcomeResults\OutcomeResult outcomeResult(int $id)
 * @method static \CanvasLMS\Api\OutcomeResults\OutcomeResult outcome_result(int $id)
 * @method static string                                      outcomeImports()
 * @method static string                                      outcome_imports()
 * @method static \CanvasLMS\Api\OutcomeImports\OutcomeImport outcomeImport(int $id)
 * @method static \CanvasLMS\Api\OutcomeImports\OutcomeImport outcome_import(int $id)
 *
 * Calendar & Scheduling:
 * @method static string                                            calendarEvents()
 * @method static string                                            calendar_events()
 * @method static \CanvasLMS\Api\CalendarEvents\CalendarEvent       calendarEvent(int $id)
 * @method static \CanvasLMS\Api\CalendarEvents\CalendarEvent       calendar_event(int $id)
 * @method static string                                            appointmentGroups()
 * @method static string                                            appointment_groups()
 * @method static \CanvasLMS\Api\AppointmentGroups\AppointmentGroup appointmentGroup(int $id)
 * @method static \CanvasLMS\Api\AppointmentGroups\AppointmentGroup appointment_group(int $id)
 *
 * Admin & Configuration:
 * @method static string                                    admins()
 * @method static \CanvasLMS\Api\Admins\Admin               admin(int $id)
 * @method static string                                    featureFlags()
 * @method static string                                    feature_flags()
 * @method static \CanvasLMS\Api\FeatureFlags\FeatureFlag   featureFlag(int $id)
 * @method static \CanvasLMS\Api\FeatureFlags\FeatureFlag   feature_flag(int $id)
 * @method static string                                    externalTools()
 * @method static string                                    external_tools()
 * @method static \CanvasLMS\Api\ExternalTools\ExternalTool externalTool(int $id)
 * @method static \CanvasLMS\Api\ExternalTools\ExternalTool external_tool(int $id)
 *
 * Content & Migration:
 * @method static string                                            contentMigrations()
 * @method static string                                            content_migrations()
 * @method static \CanvasLMS\Api\ContentMigrations\ContentMigration contentMigration(int $id)
 * @method static \CanvasLMS\Api\ContentMigrations\ContentMigration content_migration(int $id)
 * @method static string                                            progress()
 *
 * Reports & Analytics:
 * @method static string courseReports()
 * @method static string course_reports()
 * @method static string analytics()
 *
 * Authentication & User Management:
 * @method static string                      logins()
 * @method static \CanvasLMS\Api\Logins\Login login(int $id)
 *
 * Bookmarks & Favorites:
 * @method static string                            bookmarks()
 * @method static \CanvasLMS\Api\Bookmarks\Bookmark bookmark(int $id)
 *
 * Branding & Theming:
 * @method static string                                  brandConfigs()
 * @method static string                                  brand_configs()
 * @method static \CanvasLMS\Api\BrandConfigs\BrandConfig brandConfig(int $id)
 * @method static \CanvasLMS\Api\BrandConfigs\BrandConfig brand_config(int $id)
 *
 * Developer Keys:
 * @method static string                                    developerKeys()
 * @method static string                                    developer_keys()
 * @method static \CanvasLMS\Api\DeveloperKeys\DeveloperKey developerKey(int $id)
 * @method static \CanvasLMS\Api\DeveloperKeys\DeveloperKey developer_key(int $id)
 *
 * Raw API Access:
 * @method static \CanvasLMS\Canvas raw()
 *
 * @see \CanvasLMS\Laravel\CanvasManager
 */
class Canvas extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'canvas';
    }
}
