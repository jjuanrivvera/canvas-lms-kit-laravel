# Changelog

All notable changes to `canvas-lms-kit-laravel` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.2.1] - 2025-09-09

### Added
- **COMPATIBILITY**: Laravel 12 support - Updated illuminate package constraints to support Laravel 12.x
  - Added support for illuminate/support ^12.0
  - Added support for illuminate/console ^12.0 
  - Added support for illuminate/config ^12.0
  - Updated orchestra/testbench to ^10.0 for Laravel 12 testing compatibility
  - Maintains full backward compatibility with Laravel 9, 10, and 11
  - Enables usage with the latest Laravel framework versions

## [0.2.0] - 2025-09-09

### Added
- **NEW**: Support for canvas-lms-kit v1.5.0 with 8 new Canvas APIs (#16)
  - Added Course Reports API (`Canvas::courseReports()`) for comprehensive course analytics
  - Added Developer Keys API (`Canvas::developerKeys()`) for API key management
  - Added Login API (`Canvas::login()`) for session and authentication handling
  - Added Analytics API (`Canvas::analytics()`) for user engagement and course analytics
  - Added Bookmarks API (`Canvas::bookmarks()`) for user bookmark management
  - Added Brand Configs API (`Canvas::brandConfigs()`) for institutional branding
  - Support for both camelCase and snake_case method variants for all APIs
  - Multi-tenant support for all new APIs via connection management

- **NEW**: Raw Canvas API access through `Canvas::raw()` method (#16)
  - Direct access to Canvas SDK for custom API endpoints: `Canvas::raw()->get('/api/v1/custom')`
  - Supports all HTTP methods (GET, POST, PUT, DELETE) for unlimited flexibility
  - Multi-tenant raw access: `Canvas::connection('tenant')->raw()->post('/endpoint')`
  - Maintains minimal wrapper philosophy while providing unlimited API access
  - Comprehensive test coverage for raw functionality

- **NEW**: Added `CanvasManagerInterface` for better abstraction and testability
  - Created interface in `src/Contracts/CanvasManagerInterface.php` with all connection management methods
  - Updated `CanvasManager` to implement the interface for improved SOLID principles
  - Enhanced service provider to bind interface to implementation for dependency injection
  - Added comprehensive test coverage for interface implementation and mocking capabilities
  - Enables easier unit testing with mock objects and dependency injection patterns
  - Maintains full backward compatibility with no breaking changes

- **SECURITY**: Added comprehensive configuration validation to prevent invalid settings (#6)
  - Created `ConfigurationValidator` class with Canvas LMS-specific validation rules
  - Validates API keys, URLs, authentication modes, timeouts, and account IDs
  - Enforces HTTPS for Canvas URLs (with localhost development exception)
  - Validates OAuth configuration completeness and redirect URI formats
  - Provides clear, actionable error messages with fix instructions
  - Catches configuration errors at application boot time for early detection
  - Configurable validation system (enabled by default, can be disabled)
  - Performance-optimized with validation result caching and environment detection
  - Added 72 comprehensive tests covering all validation scenarios and edge cases
  - Maintains full backward compatibility with existing valid configurations

### Changed
- **DEPENDENCY**: Updated canvas-lms-kit dependency from ^1.4 to ^1.5 (#16)
  - Upgraded from v1.4.1 to v1.5.0 with 100% backward compatibility
  - Enhanced facade PHPDoc documentation for better IDE support with new APIs
  - Improved static analysis coverage by removing obsolete ignore patterns
  - Performance-optimized method resolution with static caching for faster API access

- **Refactored**: Extracted duplicated configuration logic into `ConfiguresCanvas` trait
  - Eliminated code duplication between `CanvasServiceProvider` and `CanvasManager`
  - Reduced configuration methods from 153+ lines to centralized trait implementation
  - Improved maintainability with single source of truth for Canvas LMS Kit configuration
  - Added comprehensive test coverage for configuration logic
  - Maintains full backward compatibility with no breaking changes

### Fixed
- **CRITICAL**: Fixed camelCase method resolution in CanvasManager that was causing BadMethodCallException for 32 API endpoints
  - Methods like `discussionTopics()`, `mediaObjects()`, `quizSubmissions()` now work correctly
  - Case-insensitive method resolution now properly handles all case variations
  - Maintains full backward compatibility with existing snake_case methods
- **BUG**: Fixed undefined `now()` helper function error in CanvasFake testing utilities
  - Replaced `now()` with explicit `Carbon::now()` import to prevent "Call to undefined function" errors
  - Ensures reliable timestamp recording in all Laravel testing environments
  - Added comprehensive test coverage to prevent regression
  - Maintains full backward compatibility with existing timestamp functionality
- **BUG**: Fixed CanvasFake file function error handling for non-existent files
  - Added file existence and readability checks before calling `filesize()` and `mime_content_type()`
  - Prevents warnings and errors when mocking file uploads with non-existent file paths
  - Uses sensible defaults (1024 bytes, 'application/octet-stream') for missing files
  - Maintains real file information when files exist for backward compatibility
  - Added comprehensive test coverage for all file handling scenarios

## [0.1.0] - 2025-01-05

### Added
- OAuth 2.0 authentication support with `auth_mode` configuration
- Complete API class mappings for all 36+ Canvas LMS resources
- Runtime user masquerading via `Config::asUser()` method
- Support for OAuth configuration options (client_id, client_secret, redirect_uri, tokens)
- Comprehensive PHPDoc annotations for Canvas facade with deprecation notices
- Support for new API resources: Announcements, MediaObjects, Submissions, and more

### Changed
- Documentation now actively discourages facade usage in favor of direct SDK usage
- Improved multi-connection configuration with authentication mode switching
- Enhanced CanvasManager with complete API class mappings
- Updated service provider to handle both API key and OAuth authentication modes

### Fixed
- Corrected masquerading implementation to use `Config::asUser()` instead of non-existent method
- PHPStan compatibility with new SDK API classes

## [0.0.1] - 2024-12-05

### Added
- Initial release of Canvas LMS Kit Laravel package
- Auto-configuration from Laravel's config system
- Multi-tenant connection management via `CanvasManager`
- Testing utilities with `CanvasFake` for mocking API calls
- Artisan command `canvas:test` for connection verification
- Laravel Facade support for convenient access
- Service provider with auto-discovery
- Support for Laravel 10 and 11
- Support for PHP 8.2 and 8.3
- Full test coverage with Pest testing framework
- PHPStan level 8 static analysis
- Laravel Pint code style formatting
- GitHub Actions CI/CD pipeline
- Comprehensive documentation and examples

[Unreleased]: https://github.com/jjuanrivvera/canvas-lms-kit-laravel/compare/v0.2.1...HEAD
[0.2.1]: https://github.com/jjuanrivvera/canvas-lms-kit-laravel/compare/v0.2.0...v0.2.1
[0.2.0]: https://github.com/jjuanrivvera/canvas-lms-kit-laravel/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/jjuanrivvera/canvas-lms-kit-laravel/compare/v0.0.1...v0.1.0
[0.0.1]: https://github.com/jjuanrivvera/canvas-lms-kit-laravel/releases/tag/v0.0.1