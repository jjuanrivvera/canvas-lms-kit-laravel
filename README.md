# Canvas LMS Kit for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jjuanrivvera/canvas-lms-kit-laravel.svg?style=flat-square)](https://packagist.org/packages/jjuanrivvera/canvas-lms-kit-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/jjuanrivvera/canvas-lms-kit-laravel.svg?style=flat-square)](https://packagist.org/packages/jjuanrivvera/canvas-lms-kit-laravel)
[![Tests](https://img.shields.io/github/actions/workflow/status/jjuanrivvera/canvas-lms-kit-laravel/run-tests.yml?branch=main&label=tests)](https://github.com/jjuanrivvera/canvas-lms-kit-laravel/actions)

A minimal Laravel integration package for the [Canvas LMS Kit](https://github.com/jjuanrivvera/canvas-lms-kit). Auto-configure Canvas API access, manage multiple connections, and test with ease.

## ✨ Features

- 🚀 **Zero Configuration** - Works immediately after installation
- 🔄 **Multi-Tenant Support** - Easily switch between Canvas instances
- 🧪 **Testing Utilities** - Mock Canvas API calls in your tests
- 📦 **Minimal & Focused** - Just 500 lines of code that do exactly what you need
- 🔧 **Laravel Native** - Uses Laravel's config, logging, and testing systems

## 📦 Installation

```bash
composer require jjuanrivvera/canvas-lms-kit-laravel
```

## ⚡ Quick Start

### 1. Publish Configuration

```bash
php artisan vendor:publish --tag=canvas-config
```

### 2. Add Credentials to `.env`

```env
CANVAS_API_KEY=your-api-key-here
CANVAS_BASE_URL=https://your-institution.instructure.com
CANVAS_ACCOUNT_ID=1
```

### 3. Test Your Connection

```bash
php artisan canvas:test
```

### 4. Start Using!

```php
use CanvasLMS\Api\Courses\Course;
use CanvasLMS\Api\Users\User;

// That's it! The package auto-configures everything
$courses = Course::get();
$user = User::find(123);
```

## 🎯 Why This Package?

### The Problem

Without this package, you need to manually configure Canvas LMS Kit in every controller, job, or service:

```php
// 😫 Before - Manual configuration everywhere
class CourseController 
{
    public function __construct() 
    {
        Config::setApiKey(env('CANVAS_API_KEY'));
        Config::setBaseUrl(env('CANVAS_BASE_URL'));
        Config::setAccountId(env('CANVAS_ACCOUNT_ID'));
        // ... more configuration
    }
}
```

### The Solution

This package auto-configures everything from your Laravel config:

```php
// 😊 After - Just works!
class CourseController 
{
    public function index() 
    {
        return Course::get(); // Already configured!
    }
}
```

## 📖 Usage

### Basic Usage

The package automatically configures the Canvas LMS Kit when your application boots. You can immediately use all Canvas API classes:

```php
use CanvasLMS\Api\Courses\Course;
use CanvasLMS\Api\Enrollments\Enrollment;
use CanvasLMS\Api\Assignments\Assignment;

// Get all courses
$courses = Course::get();

// Get a specific course
$course = Course::find(123);

// Create a course
$course = Course::create([
    'name' => 'Introduction to Laravel',
    'course_code' => 'LAR101',
]);

// Update a course
$course->name = 'Advanced Laravel';
$course->save();
```

### Multi-Tenant Support

Perfect for SaaS applications managing multiple Canvas instances:

```php
use CanvasLMS\Laravel\Facades\Canvas;

// Switch to a different Canvas instance
Canvas::connection('school_b');
$courses = Course::get(); // From school_b

// Temporarily use a different connection
Canvas::usingConnection('school_c', function () {
    $course = Course::find(456); // From school_c
});

// Back to default connection
Canvas::connection('main');
```

Configure multiple connections in `config/canvas.php`:

```php
'connections' => [
    'main' => [
        'api_key' => env('CANVAS_API_KEY'),
        'base_url' => env('CANVAS_BASE_URL'),
    ],
    
    'school_b' => [
        'api_key' => env('SCHOOL_B_API_KEY'),
        'base_url' => env('SCHOOL_B_BASE_URL'),
    ],
],
```

### Testing

Mock Canvas API calls in your tests:

```php
use CanvasLMS\Laravel\Testing\CanvasFake;
use CanvasLMS\Api\Courses\Course;

test('creates a course in canvas', function () {
    // Arrange
    $fake = new CanvasFake();
    $fake->fake([
        'courses' => [
            ['id' => 123, 'name' => 'Test Course'],
        ],
    ]);
    
    // Act
    $courses = Course::get();
    
    // Assert
    expect($courses)->toHaveCount(1);
    expect($courses[0]->name)->toBe('Test Course');
    $fake->assertApiCallMade('GET', '/courses');
});
```

### Facade Usage (Optional)

If you prefer facades, you can use the Canvas facade:

```php
use CanvasLMS\Laravel\Facades\Canvas;

// These are equivalent:
Course::get();                    // Direct SDK usage
Canvas::courses()::get();         // Via facade

// The facade is most useful for connection management:
Canvas::connection('tenant_2')->courses()::get();
```

## 🔧 Configuration

The configuration file (`config/canvas.php`) provides extensive customization options:

```php
return [
    // Default connection to use
    'default' => env('CANVAS_CONNECTION', 'main'),
    
    // Multiple Canvas connections
    'connections' => [
        'main' => [
            // Authentication mode: 'api_key' or 'oauth'
            'auth_mode' => env('CANVAS_AUTH_MODE', 'api_key'),
            
            // API Key authentication (when auth_mode = 'api_key')
            'api_key' => env('CANVAS_API_KEY'),
            'base_url' => env('CANVAS_BASE_URL'),
            'account_id' => env('CANVAS_ACCOUNT_ID', 1),
            'timeout' => env('CANVAS_TIMEOUT', 30),
            'log_channel' => env('CANVAS_LOG_CHANNEL'),
            
            // OAuth 2.0 authentication (when auth_mode = 'oauth')
            'oauth_client_id' => env('CANVAS_OAUTH_CLIENT_ID'),
            'oauth_client_secret' => env('CANVAS_OAUTH_CLIENT_SECRET'),
            'oauth_redirect_uri' => env('CANVAS_OAUTH_REDIRECT_URI'),
            'oauth_token' => env('CANVAS_OAUTH_TOKEN'),
            'oauth_refresh_token' => env('CANVAS_OAUTH_REFRESH_TOKEN'),
            
            // Optional middleware configuration
            'middleware' => [
                'retry' => [
                    'max_attempts' => 3,
                    'delay' => 1000,
                ],
                'rate_limit' => [
                    'wait_on_limit' => true,
                ],
            ],
        ],
    ],
    
    // Testing configuration
    'testing' => [
        'fake' => env('CANVAS_FAKE', false),
    ],
];
```

## 🔐 Authentication Methods

### API Key Authentication (Default)

The simplest authentication method using Canvas API tokens:

```env
CANVAS_AUTH_MODE=api_key  # or omit - api_key is default
CANVAS_API_KEY=your-canvas-api-token
```

### OAuth 2.0 Authentication

For OAuth-based authentication, switch the auth mode and configure OAuth settings:

```env
CANVAS_AUTH_MODE=oauth
CANVAS_OAUTH_CLIENT_ID=your-client-id
CANVAS_OAUTH_CLIENT_SECRET=your-client-secret
CANVAS_OAUTH_REDIRECT_URI=https://yourapp.com/canvas/callback
CANVAS_OAUTH_TOKEN=current-access-token
CANVAS_OAUTH_REFRESH_TOKEN=refresh-token
```

### User Masquerading (Runtime Only)

Administrators can make API calls as another user (requires appropriate permissions):

```php
use CanvasLMS\Config;

// Masquerade as a specific user for all subsequent calls
Config::asUser(12345);

// Make API calls as that user
$courses = Course::get(); // Returns courses visible to user 12345

// Stop masquerading
Config::stopMasquerading();

// Now calls are made as the authenticated user again
$courses = Course::get();
```

## 🎨 Artisan Commands

### Test Connection

Verify your Canvas API connection and see authenticated user info:

```bash
# Test default connection
php artisan canvas:test

# Test specific connection
php artisan canvas:test --connection=school_b

# Show current configuration
php artisan canvas:test --show-config

# Verbose mode (tests additional endpoints)
php artisan canvas:test -v
```

## 🧪 Testing Your Integration

### Using Pest (Recommended)

```php
use CanvasLMS\Laravel\Testing\CanvasFake;

beforeEach(function () {
    $this->canvas = new CanvasFake();
});

test('enrolls user in course', function () {
    // Arrange
    $this->canvas->fake([
        'enrollments' => [
            'id' => 999,
            'user_id' => 123,
            'course_id' => 456,
            'type' => 'StudentEnrollment',
        ],
    ]);
    
    // Act
    $enrollment = Enrollment::create([
        'enrollment' => [
            'user_id' => 123,
            'course_id' => 456,
            'type' => 'StudentEnrollment',
        ],
    ]);
    
    // Assert
    expect($enrollment->user_id)->toBe(123);
    $this->canvas->assertEnrollmentCreated(123, 456, 'StudentEnrollment');
});
```

### Available Assertions

```php
$canvas = new CanvasFake();

// Assert specific API calls
$canvas->assertCourseCreated(['name' => 'Laravel 101']);
$canvas->assertEnrollmentCreated($userId, $courseId);
$canvas->assertApiCallMade('GET', '/courses/*');
$canvas->assertApiCallCount(3);
$canvas->assertNoApiCallsMade();
```

## 🚀 Real-World Examples

### Syncing Users from Laravel to Canvas

```php
use App\Models\User;
use CanvasLMS\Api\Users\User as CanvasUser;

class SyncUsersToCanvas
{
    public function handle()
    {
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                CanvasUser::create([
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'sis_user_id' => $user->id,
                    ],
                ]);
            }
        });
    }
}
```

### Multi-Tenant Course Management

```php
use CanvasLMS\Laravel\Facades\Canvas;

class TenantCourseService
{
    public function getCoursesByTenant(string $tenant): array
    {
        return Canvas::connection($tenant)
            ->courses()
            ::get(['per_page' => 100]);
    }
    
    public function syncCourseAcrossTenants(int $courseId, array $tenants): void
    {
        $sourceCourse = Course::find($courseId);
        
        foreach ($tenants as $tenant) {
            Canvas::usingConnection($tenant, function () use ($sourceCourse) {
                Course::create([
                    'name' => $sourceCourse->name,
                    'course_code' => $sourceCourse->course_code,
                ]);
            });
        }
    }
}
```

## 📚 Available Canvas APIs

This package provides auto-configuration for all Canvas LMS Kit APIs. Use them directly:

### Core Resources
- `CanvasLMS\Api\Courses\Course` - Course management
- `CanvasLMS\Api\Users\User` - User management  
- `CanvasLMS\Api\Accounts\Account` - Account administration

### Course Components
- `CanvasLMS\Api\Enrollments\Enrollment` - User enrollments
- `CanvasLMS\Api\Assignments\Assignment` - Assignments and submissions
- `CanvasLMS\Api\Modules\Module` - Course modules and items
- `CanvasLMS\Api\Pages\Page` - Wiki pages
- `CanvasLMS\Api\Sections\Section` - Course sections
- `CanvasLMS\Api\Tabs\Tab` - Course navigation tabs
- `CanvasLMS\Api\Announcements\Announcement` - Course announcements

### Discussions & Communication
- `CanvasLMS\Api\DiscussionTopics\DiscussionTopic` - Discussion forums
- `CanvasLMS\Api\Conversations\Conversation` - Internal messaging
- `CanvasLMS\Api\Conferences\Conference` - Web conferences

### Files & Media
- `CanvasLMS\Api\Files\File` - File uploads and management
- `CanvasLMS\Api\MediaObjects\MediaObject` - Media/video content

### Grading & Assessment  
- `CanvasLMS\Api\Quizzes\Quiz` - Quiz creation and management
- `CanvasLMS\Api\QuizSubmissions\QuizSubmission` - Quiz attempts
- `CanvasLMS\Api\Submissions\Submission` - Assignment submissions
- `CanvasLMS\Api\SubmissionComments\SubmissionComment` - Submission feedback
- `CanvasLMS\Api\Rubrics\Rubric` - Grading rubrics
- `CanvasLMS\Api\GradebookHistory\GradebookHistory` - Grade audit trail

### Groups & Collaboration
- `CanvasLMS\Api\Groups\Group` - Student groups
- `CanvasLMS\Api\GroupCategories\GroupCategory` - Group sets

### Outcomes & Standards
- `CanvasLMS\Api\Outcomes\Outcome` - Learning outcomes
- `CanvasLMS\Api\OutcomeGroups\OutcomeGroup` - Outcome organization
- `CanvasLMS\Api\OutcomeResults\OutcomeResult` - Outcome assessments
- `CanvasLMS\Api\OutcomeImports\OutcomeImport` - Bulk outcome imports

### Calendar & Scheduling
- `CanvasLMS\Api\CalendarEvents\CalendarEvent` - Calendar entries
- `CanvasLMS\Api\AppointmentGroups\AppointmentGroup` - Office hours/appointments

### Admin & Configuration
- `CanvasLMS\Api\Admins\Admin` - Account administrators
- `CanvasLMS\Api\FeatureFlags\FeatureFlag` - Feature toggles
- `CanvasLMS\Api\ExternalTools\ExternalTool` - LTI integrations
- `CanvasLMS\Api\ContentMigrations\ContentMigration` - Course copy/import
- `CanvasLMS\Api\Progress\Progress` - Long-running job status

See the [Canvas LMS Kit documentation](https://github.com/jjuanrivvera/canvas-lms-kit) for complete API reference.

## 🤝 Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

### Development

```bash
# Install dependencies
composer install

# Run tests
composer test

# Run tests with coverage
composer test-coverage

# Format code
composer format

# Run static analysis
composer analyse

# Run all checks
composer check
```

## 📝 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## 🙏 Credits

- [Juan Rivera](https://github.com/jjuanrivvera)
- [All Contributors](../../contributors)

## 🔗 Related Packages

- [Canvas LMS Kit](https://github.com/jjuanrivvera/canvas-lms-kit) - The base PHP SDK
- [Canvas LMS API Documentation](https://canvas.instructure.com/doc/api/) - Official Canvas API docs

## 🆘 Support

If you encounter any issues or have questions:

1. Check the [FAQ](#faq) section below
2. Search through [existing issues](https://github.com/jjuanrivvera/canvas-lms-kit-laravel/issues)
3. Create a new issue with a clear description

## ❓ FAQ

**Q: Do I need to manually initialize the Canvas API?**  
A: No! This package auto-configures everything when Laravel boots.

**Q: Can I use this with multiple Canvas instances?**  
A: Yes! Configure multiple connections and switch between them using `Canvas::connection()`.

**Q: How do I test Canvas API calls?**  
A: Use the included `CanvasFake` class to mock API responses in your tests.

**Q: Is this package compatible with Laravel Octane?**  
A: Yes, the package is stateless and works with Laravel Octane.

**Q: What Laravel versions are supported?**  
A: Laravel 9.x, 10.x, and 11.x are fully supported.