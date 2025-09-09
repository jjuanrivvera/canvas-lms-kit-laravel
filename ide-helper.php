<?php

/**
 * IDE Helper for Canvas LMS Kit Laravel Package.
 *
 * This file provides additional IDE autocomplete support for dynamic methods
 * and facades. It should not be included in runtime, only used for IDE analysis.
 *
 * @author Canvas LMS Kit Laravel
 */

namespace {
    exit('This file should not be included, only analyzed by your IDE');
}

namespace CanvasLMS\Laravel\Facades {
    /**
     * Canvas Facade IDE Helper.
     *
     * Provides autocomplete support for all Canvas API methods accessed through the facade.
     *
     * Connection Management:
     *
     * @method static \CanvasLMS\Laravel\CanvasManager connection(string $name)                                Switch to a different Canvas connection
     * @method static string                           getConnection()                                         Get the current connection name
     * @method static array|null                       getConnectionConfig()                                   Get the configuration for the current connection
     * @method static array<string>                    getAvailableConnections()                               Get all available connection names
     * @method static mixed                            usingConnection(string $connection, callable $callback) Execute a callback using a specific connection
     *
     * Core Resources:
     * @method static string                          courses()        Access Course API
     * @method static \CanvasLMS\Api\Courses\Course   course(int $id)  Find a specific course
     * @method static string                          users()          Access User API
     * @method static \CanvasLMS\Api\Users\User       user(int $id)    Find a specific user
     * @method static string                          accounts()       Access Account API
     * @method static \CanvasLMS\Api\Accounts\Account account(int $id) Find a specific account
     *
     * Course Components:
     * @method static string                                    enrollments()         Access Enrollment API
     * @method static \CanvasLMS\Api\Enrollments\Enrollment     enrollment(int $id)   Find a specific enrollment
     * @method static string                                    assignments()         Access Assignment API
     * @method static \CanvasLMS\Api\Assignments\Assignment     assignment(int $id)   Find a specific assignment
     * @method static string                                    modules()             Access Module API
     * @method static \CanvasLMS\Api\Modules\Module             module(int $id)       Find a specific module
     * @method static string                                    pages()               Access Page API
     * @method static \CanvasLMS\Api\Pages\Page                 page(int $id)         Find a specific page
     * @method static string                                    sections()            Access Section API
     * @method static \CanvasLMS\Api\Sections\Section           section(int $id)      Find a specific section
     * @method static string                                    tabs()                Access Tab API
     * @method static \CanvasLMS\Api\Tabs\Tab                   tab(int $id)          Find a specific tab
     * @method static string                                    announcements()       Access Announcement API
     * @method static \CanvasLMS\Api\Announcements\Announcement announcement(int $id) Find a specific announcement
     *
     * Discussions & Communication:
     * @method static string                                          discussionTopics() Access Discussion Topic API (camelCase)
     * @method static string                                          discussion_topics() Access Discussion Topic API (snake_case)
     * @method static \CanvasLMS\Api\DiscussionTopics\DiscussionTopic discussionTopic(int $id) Find a specific discussion topic (camelCase)
     * @method static \CanvasLMS\Api\DiscussionTopics\DiscussionTopic discussion_topic(int $id) Find a specific discussion topic (snake_case)
     * @method static string                                          conversations()                                                         Access Conversation API
     * @method static \CanvasLMS\Api\Conversations\Conversation       conversation(int $id)                                                   Find a specific conversation
     * @method static string                                          conferences()                                                           Access Conference API
     * @method static \CanvasLMS\Api\Conferences\Conference           conference(int $id)                                                     Find a specific conference
     *
     * Files & Media:
     * @method static string                                  files()                                                         Access File API
     * @method static \CanvasLMS\Api\Files\File               file(int $id)                                                   Find a specific file
     * @method static string                                  mediaObjects() Access Media Object API (camelCase)
     * @method static string                                  media_objects() Access Media Object API (snake_case)
     * @method static \CanvasLMS\Api\MediaObjects\MediaObject mediaObject(int $id) Find a specific media object (camelCase)
     * @method static \CanvasLMS\Api\MediaObjects\MediaObject media_object(int $id) Find a specific media object (snake_case)
     *
     * Grading & Assessment:
     * @method static string                                              quizzes()                                                                   Access Quiz API
     * @method static \CanvasLMS\Api\Quizzes\Quiz                         quiz(int $id)                                                               Find a specific quiz
     * @method static string                                              quizSubmissions() Access Quiz Submission API (camelCase)
     * @method static string                                              quiz_submissions() Access Quiz Submission API (snake_case)
     * @method static \CanvasLMS\Api\QuizSubmissions\QuizSubmission       quizSubmission(int $id) Find a specific quiz submission (camelCase)
     * @method static \CanvasLMS\Api\QuizSubmissions\QuizSubmission       quiz_submission(int $id) Find a specific quiz submission (snake_case)
     * @method static string                                              submissions()                                                               Access Submission API
     * @method static \CanvasLMS\Api\Submissions\Submission               submission(int $id)                                                         Find a specific submission
     * @method static string                                              submissionComments() Access Submission Comment API (camelCase)
     * @method static string                                              submission_comments() Access Submission Comment API (snake_case)
     * @method static \CanvasLMS\Api\SubmissionComments\SubmissionComment submissionComment(int $id) Find a specific submission comment (camelCase)
     * @method static \CanvasLMS\Api\SubmissionComments\SubmissionComment submission_comment(int $id) Find a specific submission comment (snake_case)
     * @method static string                                              rubrics()                                                                   Access Rubric API
     * @method static \CanvasLMS\Api\Rubrics\Rubric                       rubric(int $id)                                                             Find a specific rubric
     * @method static string                                              gradebookHistory() Access Gradebook History API (camelCase)
     * @method static string                                              gradebook_history() Access Gradebook History API (snake_case)
     *
     * Groups & Collaboration:
     * @method static string                                       groups()                                                            Access Group API
     * @method static \CanvasLMS\Api\Groups\Group                  group(int $id)                                                      Find a specific group
     * @method static string                                       groupCategories() Access Group Category API (camelCase)
     * @method static string                                       group_categories() Access Group Category API (snake_case)
     * @method static \CanvasLMS\Api\GroupCategories\GroupCategory groupCategory(int $id) Find a specific group category (camelCase)
     * @method static \CanvasLMS\Api\GroupCategories\GroupCategory group_category(int $id) Find a specific group category (snake_case)
     *
     * Outcomes & Standards:
     * @method static string                                      outcomes()                                                          Access Outcome API
     * @method static \CanvasLMS\Api\Outcomes\Outcome             outcome(int $id)                                                    Find a specific outcome
     * @method static string                                      outcomeGroups() Access Outcome Group API (camelCase)
     * @method static string                                      outcome_groups() Access Outcome Group API (snake_case)
     * @method static \CanvasLMS\Api\OutcomeGroups\OutcomeGroup   outcomeGroup(int $id) Find a specific outcome group (camelCase)
     * @method static \CanvasLMS\Api\OutcomeGroups\OutcomeGroup   outcome_group(int $id) Find a specific outcome group (snake_case)
     * @method static string                                      outcomeResults() Access Outcome Result API (camelCase)
     * @method static string                                      outcome_results() Access Outcome Result API (snake_case)
     * @method static \CanvasLMS\Api\OutcomeResults\OutcomeResult outcomeResult(int $id) Find a specific outcome result (camelCase)
     * @method static \CanvasLMS\Api\OutcomeResults\OutcomeResult outcome_result(int $id) Find a specific outcome result (snake_case)
     * @method static string                                      outcomeImports() Access Outcome Import API (camelCase)
     * @method static string                                      outcome_imports() Access Outcome Import API (snake_case)
     * @method static \CanvasLMS\Api\OutcomeImports\OutcomeImport outcomeImport(int $id) Find a specific outcome import (camelCase)
     * @method static \CanvasLMS\Api\OutcomeImports\OutcomeImport outcome_import(int $id) Find a specific outcome import (snake_case)
     *
     * Calendar & Scheduling:
     * @method static string                                            calendarEvents() Access Calendar Event API (camelCase)
     * @method static string                                            calendar_events() Access Calendar Event API (snake_case)
     * @method static \CanvasLMS\Api\CalendarEvents\CalendarEvent       calendarEvent(int $id) Find a specific calendar event (camelCase)
     * @method static \CanvasLMS\Api\CalendarEvents\CalendarEvent       calendar_event(int $id) Find a specific calendar event (snake_case)
     * @method static string                                            appointmentGroups() Access Appointment Group API (camelCase)
     * @method static string                                            appointment_groups() Access Appointment Group API (snake_case)
     * @method static \CanvasLMS\Api\AppointmentGroups\AppointmentGroup appointmentGroup(int $id) Find a specific appointment group (camelCase)
     * @method static \CanvasLMS\Api\AppointmentGroups\AppointmentGroup appointment_group(int $id) Find a specific appointment group (snake_case)
     *
     * Admin & Configuration:
     * @method static string                                    admins()                                                          Access Admin API
     * @method static \CanvasLMS\Api\Admins\Admin               admin(int $id)                                                    Find a specific admin
     * @method static string                                    featureFlags() Access Feature Flag API (camelCase)
     * @method static string                                    feature_flags() Access Feature Flag API (snake_case)
     * @method static \CanvasLMS\Api\FeatureFlags\FeatureFlag   featureFlag(int $id) Find a specific feature flag (camelCase)
     * @method static \CanvasLMS\Api\FeatureFlags\FeatureFlag   feature_flag(int $id) Find a specific feature flag (snake_case)
     * @method static string                                    externalTools() Access External Tool API (camelCase)
     * @method static string                                    external_tools() Access External Tool API (snake_case)
     * @method static \CanvasLMS\Api\ExternalTools\ExternalTool externalTool(int $id) Find a specific external tool (camelCase)
     * @method static \CanvasLMS\Api\ExternalTools\ExternalTool external_tool(int $id) Find a specific external tool (snake_case)
     *
     * Content & Migration:
     * @method static string                                            contentMigrations() Access Content Migration API (camelCase)
     * @method static string                                            content_migrations() Access Content Migration API (snake_case)
     * @method static \CanvasLMS\Api\ContentMigrations\ContentMigration contentMigration(int $id) Find a specific content migration (camelCase)
     * @method static \CanvasLMS\Api\ContentMigrations\ContentMigration content_migration(int $id) Find a specific content migration (snake_case)
     * @method static string                                            progress()                                                                Access Progress API
     */
    class Canvas
    {
        //
    }
}

namespace CanvasLMS\Laravel {
    /**
     * Canvas Manager IDE Helper.
     *
     * Provides autocomplete support for all Canvas API methods accessed through the manager.
     *
     * Connection Management:
     *
     * @method \CanvasLMS\Laravel\CanvasManager connection(string $name)                                Switch to a different Canvas connection
     * @method string                           getConnection()                                         Get the current connection name
     * @method array|null                       getConnectionConfig()                                   Get the configuration for the current connection
     * @method array<string>                    getAvailableConnections()                               Get all available connection names
     * @method mixed                            usingConnection(string $connection, callable $callback) Execute a callback using a specific connection
     *
     * Core Resources:
     * @method string                          courses()        Access Course API
     * @method \CanvasLMS\Api\Courses\Course   course(int $id)  Find a specific course
     * @method string                          users()          Access User API
     * @method \CanvasLMS\Api\Users\User       user(int $id)    Find a specific user
     * @method string                          accounts()       Access Account API
     * @method \CanvasLMS\Api\Accounts\Account account(int $id) Find a specific account
     *
     * Course Components:
     * @method string                                    enrollments()         Access Enrollment API
     * @method \CanvasLMS\Api\Enrollments\Enrollment     enrollment(int $id)   Find a specific enrollment
     * @method string                                    assignments()         Access Assignment API
     * @method \CanvasLMS\Api\Assignments\Assignment     assignment(int $id)   Find a specific assignment
     * @method string                                    modules()             Access Module API
     * @method \CanvasLMS\Api\Modules\Module             module(int $id)       Find a specific module
     * @method string                                    pages()               Access Page API
     * @method \CanvasLMS\Api\Pages\Page                 page(int $id)         Find a specific page
     * @method string                                    sections()            Access Section API
     * @method \CanvasLMS\Api\Sections\Section           section(int $id)      Find a specific section
     * @method string                                    tabs()                Access Tab API
     * @method \CanvasLMS\Api\Tabs\Tab                   tab(int $id)          Find a specific tab
     * @method string                                    announcements()       Access Announcement API
     * @method \CanvasLMS\Api\Announcements\Announcement announcement(int $id) Find a specific announcement
     *
     * Discussions:
     * @method string                                          discussionTopics() Access Discussion Topic API (camelCase)
     * @method string                                          discussion_topics() Access Discussion Topic API (snake_case)
     * @method \CanvasLMS\Api\DiscussionTopics\DiscussionTopic discussionTopic(int $id) Find a specific discussion topic (camelCase)
     * @method \CanvasLMS\Api\DiscussionTopics\DiscussionTopic discussion_topic(int $id) Find a specific discussion topic (snake_case)
     *
     * Files & Media:
     * @method string                                  files()                                                         Access File API
     * @method \CanvasLMS\Api\Files\File               file(int $id)                                                   Find a specific file
     * @method string                                  mediaObjects() Access Media Object API (camelCase)
     * @method string                                  media_objects() Access Media Object API (snake_case)
     * @method \CanvasLMS\Api\MediaObjects\MediaObject mediaObject(int $id) Find a specific media object (camelCase)
     * @method \CanvasLMS\Api\MediaObjects\MediaObject media_object(int $id) Find a specific media object (snake_case)
     *
     * Grading & Assessment:
     * @method string                                              quizzes()                                                                   Access Quiz API
     * @method \CanvasLMS\Api\Quizzes\Quiz                         quiz(int $id)                                                               Find a specific quiz
     * @method string                                              quizSubmissions() Access Quiz Submission API (camelCase)
     * @method string                                              quiz_submissions() Access Quiz Submission API (snake_case)
     * @method \CanvasLMS\Api\QuizSubmissions\QuizSubmission       quizSubmission(int $id) Find a specific quiz submission (camelCase)
     * @method \CanvasLMS\Api\QuizSubmissions\QuizSubmission       quiz_submission(int $id) Find a specific quiz submission (snake_case)
     * @method string                                              submissions()                                                               Access Submission API
     * @method \CanvasLMS\Api\Submissions\Submission               submission(int $id)                                                         Find a specific submission
     * @method string                                              submissionComments() Access Submission Comment API (camelCase)
     * @method string                                              submission_comments() Access Submission Comment API (snake_case)
     * @method \CanvasLMS\Api\SubmissionComments\SubmissionComment submissionComment(int $id) Find a specific submission comment (camelCase)
     * @method \CanvasLMS\Api\SubmissionComments\SubmissionComment submission_comment(int $id) Find a specific submission comment (snake_case)
     * @method string                                              rubrics()                                                                   Access Rubric API
     * @method \CanvasLMS\Api\Rubrics\Rubric                       rubric(int $id)                                                             Find a specific rubric
     * @method string                                              gradebookHistory() Access Gradebook History API (camelCase)
     * @method string                                              gradebook_history() Access Gradebook History API (snake_case)
     *
     * Groups:
     * @method string                                       groups()                                                            Access Group API
     * @method \CanvasLMS\Api\Groups\Group                  group(int $id)                                                      Find a specific group
     * @method string                                       groupCategories() Access Group Category API (camelCase)
     * @method string                                       group_categories() Access Group Category API (snake_case)
     * @method \CanvasLMS\Api\GroupCategories\GroupCategory groupCategory(int $id) Find a specific group category (camelCase)
     * @method \CanvasLMS\Api\GroupCategories\GroupCategory group_category(int $id) Find a specific group category (snake_case)
     *
     * Outcomes:
     * @method string                                      outcomes()                                                          Access Outcome API
     * @method \CanvasLMS\Api\Outcomes\Outcome             outcome(int $id)                                                    Find a specific outcome
     * @method string                                      outcomeGroups() Access Outcome Group API (camelCase)
     * @method string                                      outcome_groups() Access Outcome Group API (snake_case)
     * @method \CanvasLMS\Api\OutcomeGroups\OutcomeGroup   outcomeGroup(int $id) Find a specific outcome group (camelCase)
     * @method \CanvasLMS\Api\OutcomeGroups\OutcomeGroup   outcome_group(int $id) Find a specific outcome group (snake_case)
     * @method string                                      outcomeResults() Access Outcome Result API (camelCase)
     * @method string                                      outcome_results() Access Outcome Result API (snake_case)
     * @method \CanvasLMS\Api\OutcomeResults\OutcomeResult outcomeResult(int $id) Find a specific outcome result (camelCase)
     * @method \CanvasLMS\Api\OutcomeResults\OutcomeResult outcome_result(int $id) Find a specific outcome result (snake_case)
     * @method string                                      outcomeImports() Access Outcome Import API (camelCase)
     * @method string                                      outcome_imports() Access Outcome Import API (snake_case)
     * @method \CanvasLMS\Api\OutcomeImports\OutcomeImport outcomeImport(int $id) Find a specific outcome import (camelCase)
     * @method \CanvasLMS\Api\OutcomeImports\OutcomeImport outcome_import(int $id) Find a specific outcome import (snake_case)
     *
     * Calendar & Scheduling:
     * @method string                                            calendarEvents() Access Calendar Event API (camelCase)
     * @method string                                            calendar_events() Access Calendar Event API (snake_case)
     * @method \CanvasLMS\Api\CalendarEvents\CalendarEvent       calendarEvent(int $id) Find a specific calendar event (camelCase)
     * @method \CanvasLMS\Api\CalendarEvents\CalendarEvent       calendar_event(int $id) Find a specific calendar event (snake_case)
     * @method string                                            appointmentGroups() Access Appointment Group API (camelCase)
     * @method string                                            appointment_groups() Access Appointment Group API (snake_case)
     * @method \CanvasLMS\Api\AppointmentGroups\AppointmentGroup appointmentGroup(int $id) Find a specific appointment group (camelCase)
     * @method \CanvasLMS\Api\AppointmentGroups\AppointmentGroup appointment_group(int $id) Find a specific appointment group (snake_case)
     *
     * Communication:
     * @method string                                    conversations()       Access Conversation API
     * @method \CanvasLMS\Api\Conversations\Conversation conversation(int $id) Find a specific conversation
     * @method string                                    conferences()         Access Conference API
     * @method \CanvasLMS\Api\Conferences\Conference     conference(int $id)   Find a specific conference
     *
     * Admin & Configuration:
     * @method string                                    admins()                                                          Access Admin API
     * @method \CanvasLMS\Api\Admins\Admin               admin(int $id)                                                    Find a specific admin
     * @method string                                    featureFlags() Access Feature Flag API (camelCase)
     * @method string                                    feature_flags() Access Feature Flag API (snake_case)
     * @method \CanvasLMS\Api\FeatureFlags\FeatureFlag   featureFlag(int $id) Find a specific feature flag (camelCase)
     * @method \CanvasLMS\Api\FeatureFlags\FeatureFlag   feature_flag(int $id) Find a specific feature flag (snake_case)
     * @method string                                    externalTools() Access External Tool API (camelCase)
     * @method string                                    external_tools() Access External Tool API (snake_case)
     * @method \CanvasLMS\Api\ExternalTools\ExternalTool externalTool(int $id) Find a specific external tool (camelCase)
     * @method \CanvasLMS\Api\ExternalTools\ExternalTool external_tool(int $id) Find a specific external tool (snake_case)
     *
     * Content & Migration:
     * @method string                                            contentMigrations() Access Content Migration API (camelCase)
     * @method string                                            content_migrations() Access Content Migration API (snake_case)
     * @method \CanvasLMS\Api\ContentMigrations\ContentMigration contentMigration(int $id) Find a specific content migration (camelCase)
     * @method \CanvasLMS\Api\ContentMigrations\ContentMigration content_migration(int $id) Find a specific content migration (snake_case)
     * @method string                                            progress()                                                                Access Progress API
     */
    class CanvasManager
    {
        //
    }
}
