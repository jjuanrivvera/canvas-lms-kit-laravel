# Changelog

All notable changes to `canvas-lms-kit-laravel` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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

[Unreleased]: https://github.com/jjuanrivvera/canvas-lms-kit-laravel/compare/v0.1.0...HEAD
[0.1.0]: https://github.com/jjuanrivvera/canvas-lms-kit-laravel/compare/v0.0.1...v0.1.0
[0.0.1]: https://github.com/jjuanrivvera/canvas-lms-kit-laravel/releases/tag/v0.0.1