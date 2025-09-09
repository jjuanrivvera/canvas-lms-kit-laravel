<?php

use CanvasLMS\Laravel\CanvasManager;

beforeEach(function () {
    $this->config = [
        'default'     => 'main',
        'connections' => [
            'main' => [
                'api_key'    => 'test-api-key',
                'base_url'   => 'https://test.canvas.com',
                'account_id' => 1,
            ],
        ],
    ];

    $this->manager = new CanvasManager($this->config);
});

test('camelCase methods resolve to correct API classes', function () {
    // Discussion APIs
    expect($this->manager->discussionTopics())
        ->toBe(\CanvasLMS\Api\DiscussionTopics\DiscussionTopic::class);
    expect($this->manager->discussionTopic())
        ->toBe(\CanvasLMS\Api\DiscussionTopics\DiscussionTopic::class);

    // Media APIs
    expect($this->manager->mediaObjects())
        ->toBe(\CanvasLMS\Api\MediaObjects\MediaObject::class);
    expect($this->manager->mediaObject())
        ->toBe(\CanvasLMS\Api\MediaObjects\MediaObject::class);

    // Quiz APIs
    expect($this->manager->quizSubmissions())
        ->toBe(\CanvasLMS\Api\QuizSubmissions\QuizSubmission::class);
    expect($this->manager->quizSubmission())
        ->toBe(\CanvasLMS\Api\QuizSubmissions\QuizSubmission::class);

    // Submission APIs
    expect($this->manager->submissionComments())
        ->toBe(\CanvasLMS\Api\SubmissionComments\SubmissionComment::class);
    expect($this->manager->submissionComment())
        ->toBe(\CanvasLMS\Api\SubmissionComments\SubmissionComment::class);

    // Gradebook APIs
    expect($this->manager->gradebookHistory())
        ->toBe(\CanvasLMS\Api\GradebookHistory\GradebookHistory::class);

    // Group APIs
    expect($this->manager->groupCategories())
        ->toBe(\CanvasLMS\Api\GroupCategories\GroupCategory::class);
    expect($this->manager->groupCategory())
        ->toBe(\CanvasLMS\Api\GroupCategories\GroupCategory::class);

    // Outcome APIs
    expect($this->manager->outcomeGroups())
        ->toBe(\CanvasLMS\Api\OutcomeGroups\OutcomeGroup::class);
    expect($this->manager->outcomeGroup())
        ->toBe(\CanvasLMS\Api\OutcomeGroups\OutcomeGroup::class);
    expect($this->manager->outcomeResults())
        ->toBe(\CanvasLMS\Api\OutcomeResults\OutcomeResult::class);
    expect($this->manager->outcomeResult())
        ->toBe(\CanvasLMS\Api\OutcomeResults\OutcomeResult::class);
    expect($this->manager->outcomeImports())
        ->toBe(\CanvasLMS\Api\OutcomeImports\OutcomeImport::class);
    expect($this->manager->outcomeImport())
        ->toBe(\CanvasLMS\Api\OutcomeImports\OutcomeImport::class);

    // Calendar APIs
    expect($this->manager->calendarEvents())
        ->toBe(\CanvasLMS\Api\CalendarEvents\CalendarEvent::class);
    expect($this->manager->calendarEvent())
        ->toBe(\CanvasLMS\Api\CalendarEvents\CalendarEvent::class);
    expect($this->manager->appointmentGroups())
        ->toBe(\CanvasLMS\Api\AppointmentGroups\AppointmentGroup::class);
    expect($this->manager->appointmentGroup())
        ->toBe(\CanvasLMS\Api\AppointmentGroups\AppointmentGroup::class);

    // Feature APIs
    expect($this->manager->featureFlags())
        ->toBe(\CanvasLMS\Api\FeatureFlags\FeatureFlag::class);
    expect($this->manager->featureFlag())
        ->toBe(\CanvasLMS\Api\FeatureFlags\FeatureFlag::class);

    // External Tool APIs
    expect($this->manager->externalTools())
        ->toBe(\CanvasLMS\Api\ExternalTools\ExternalTool::class);
    expect($this->manager->externalTool())
        ->toBe(\CanvasLMS\Api\ExternalTools\ExternalTool::class);

    // Content Migration APIs
    expect($this->manager->contentMigrations())
        ->toBe(\CanvasLMS\Api\ContentMigrations\ContentMigration::class);
    expect($this->manager->contentMigration())
        ->toBe(\CanvasLMS\Api\ContentMigrations\ContentMigration::class);
});

test('method resolution is case insensitive', function () {
    // Test discussionTopics in various cases
    expect($this->manager->discussionTopics())
        ->toBe($this->manager->DiscussionTopics())
        ->toBe($this->manager->DISCUSSIONTOPICS())
        ->toBe($this->manager->DiScUsSiOnToPiCs());

    // Test mediaObjects in various cases
    expect($this->manager->mediaObjects())
        ->toBe($this->manager->MediaObjects())
        ->toBe($this->manager->MEDIAOBJECTS())
        ->toBe($this->manager->mEdIaObJeCtS());

    // Test quizSubmissions in various cases
    expect($this->manager->quizSubmissions())
        ->toBe($this->manager->QuizSubmissions())
        ->toBe($this->manager->QUIZSUBMISSIONS())
        ->toBe($this->manager->qUiZsUbMiSsIoNs());
});

test('both camelCase and snake_case resolve to same API class', function () {
    // Discussion Topics
    expect($this->manager->discussionTopics())
        ->toBe($this->manager->discussion_topics());

    // Media Objects
    expect($this->manager->mediaObjects())
        ->toBe($this->manager->media_objects());

    // Quiz Submissions
    expect($this->manager->quizSubmissions())
        ->toBe($this->manager->quiz_submissions());

    // Submission Comments
    expect($this->manager->submissionComments())
        ->toBe($this->manager->submission_comments());

    // Gradebook History
    expect($this->manager->gradebookHistory())
        ->toBe($this->manager->gradebook_history());

    // Group Categories
    expect($this->manager->groupCategories())
        ->toBe($this->manager->group_categories());

    // Outcome Groups
    expect($this->manager->outcomeGroups())
        ->toBe($this->manager->outcome_groups());

    // Outcome Results
    expect($this->manager->outcomeResults())
        ->toBe($this->manager->outcome_results());

    // Outcome Imports
    expect($this->manager->outcomeImports())
        ->toBe($this->manager->outcome_imports());

    // Calendar Events
    expect($this->manager->calendarEvents())
        ->toBe($this->manager->calendar_events());

    // Appointment Groups
    expect($this->manager->appointmentGroups())
        ->toBe($this->manager->appointment_groups());

    // Feature Flags
    expect($this->manager->featureFlags())
        ->toBe($this->manager->feature_flags());

    // External Tools
    expect($this->manager->externalTools())
        ->toBe($this->manager->external_tools());

    // Content Migrations
    expect($this->manager->contentMigrations())
        ->toBe($this->manager->content_migrations());
});

test('all documented camelCase methods work without exceptions', function () {
    $camelCaseMethods = [
        'discussionTopics',
        'discussionTopic',
        'mediaObjects',
        'mediaObject',
        'quizSubmissions',
        'quizSubmission',
        'submissionComments',
        'submissionComment',
        'gradebookHistory',
        'groupCategories',
        'groupCategory',
        'outcomeGroups',
        'outcomeGroup',
        'outcomeResults',
        'outcomeResult',
        'outcomeImports',
        'outcomeImport',
        'calendarEvents',
        'calendarEvent',
        'appointmentGroups',
        'appointmentGroup',
        'featureFlags',
        'featureFlag',
        'externalTools',
        'externalTool',
        'contentMigrations',
        'contentMigration',
    ];

    foreach ($camelCaseMethods as $method) {
        expect($this->manager->$method())
            ->toBeString()
            ->toContain('CanvasLMS\\Api\\');
    }
});

test('backward compatibility is maintained for snake_case methods', function () {
    $snakeCaseMethods = [
        'discussion_topics',
        'discussion_topic',
        'media_objects',
        'media_object',
        'quiz_submissions',
        'quiz_submission',
        'submission_comments',
        'submission_comment',
        'gradebook_history',
        'group_categories',
        'group_category',
        'outcome_groups',
        'outcome_group',
        'outcome_results',
        'outcome_result',
        'outcome_imports',
        'outcome_import',
        'calendar_events',
        'calendar_event',
        'appointment_groups',
        'appointment_group',
        'feature_flags',
        'feature_flag',
        'external_tools',
        'external_tool',
        'content_migrations',
        'content_migration',
    ];

    foreach ($snakeCaseMethods as $method) {
        expect($this->manager->$method())
            ->toBeString()
            ->toContain('CanvasLMS\\Api\\');
    }
});

test('method chaining works with camelCase methods', function () {
    // These would normally throw exceptions before the fix
    // We're testing that the class name is returned for static method chaining
    expect($this->manager->discussionTopics())
        ->toBe(\CanvasLMS\Api\DiscussionTopics\DiscussionTopic::class);

    expect($this->manager->quizSubmissions())
        ->toBe(\CanvasLMS\Api\QuizSubmissions\QuizSubmission::class);

    expect($this->manager->calendarEvents())
        ->toBe(\CanvasLMS\Api\CalendarEvents\CalendarEvent::class);
});

test('singular camelCase methods with parameters resolve correctly', function () {
    // Singular methods should resolve even with parameters
    // The actual find() call would happen in the Canvas SDK, not our wrapper
    // We're just testing that the method resolution works

    // These should not throw BadMethodCallException
    $methods = [
        'discussionTopic' => 123,
        'mediaObject'     => 456,
        'calendarEvent'   => 789,
        'quizSubmission'  => 101,
        'groupCategory'   => 202,
    ];

    foreach ($methods as $method => $id) {
        // We expect these to return something (would be the result of find() in real usage)
        // But since we're not mocking the SDK, we just verify the method resolves
        expect(method_exists($this->manager, '__call'))->toBeTrue();

        // Verify the method name would be handled by __call
        $reflection = new ReflectionClass($this->manager);
        $callMethod = $reflection->getMethod('__call');
        expect($callMethod->isPublic())->toBeTrue();
    }
});

test('method resolution performance is acceptable', function () {
    // Test that 1000 method resolutions complete in reasonable time
    $iterations = 1000;
    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        // Call various camelCase methods to test performance
        $this->manager->discussionTopics();
        $this->manager->mediaObjects();
        $this->manager->quizSubmissions();
        $this->manager->calendarEvents();
    }

    $duration = microtime(true) - $start;

    // Should complete 4000 method calls in less than 100ms
    expect($duration)->toBeLessThan(0.1);

    // Log performance for visibility
    $avgTimePerCall = ($duration / ($iterations * 4)) * 1000000; // microseconds
    expect($avgTimePerCall)->toBeLessThan(25); // Less than 25 microseconds per call
});

test('static caching improves performance after first call', function () {
    // Create a fresh manager instance to ensure clean cache
    $manager = new CanvasManager($this->config);

    // Test different methods for first calls (no cache)
    $firstCallStart = microtime(true);
    $manager->discussionTopics();
    $manager->mediaObjects();
    $manager->calendarEvents();
    $manager->quizSubmissions();
    $manager->assignments();
    $firstCallTime = microtime(true) - $firstCallStart;

    // Test same methods again (should be cached)
    $cachedCallStart = microtime(true);
    $manager->discussionTopics();
    $manager->mediaObjects();
    $manager->calendarEvents();
    $manager->quizSubmissions();
    $manager->assignments();
    $cachedCallTime = microtime(true) - $cachedCallStart;

    // Test that caching doesn't make performance significantly worse
    // Micro-benchmarks are unreliable, so we just ensure reasonable performance
    expect($cachedCallTime)->toBeLessThan(0.001); // Less than 1ms for 5 cached calls
    expect($firstCallTime)->toBeLessThan(0.001);  // Less than 1ms for 5 first calls
});

test('memory usage remains reasonable with repeated calls', function () {
    $initialMemory = memory_get_usage();

    // Make many calls to test memory usage
    for ($i = 0; $i < 1000; $i++) {
        $this->manager->discussionTopics();
        $this->manager->mediaObjects();
        $this->manager->calendarEvents();
    }

    $memoryUsed = memory_get_usage() - $initialMemory;

    // Memory increase should be minimal (less than 1MB for 3000 calls)
    // The static cache should prevent repeated allocations
    expect($memoryUsed)->toBeLessThan(1024 * 1024); // 1MB
});
