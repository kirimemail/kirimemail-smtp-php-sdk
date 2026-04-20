# Changelog

All notable changes to this project will be documented in this file.

## [1.5.0] - 2026-04-20

### Added
- Laravel Service Provider (`KirimEmail\Smtp\Laravel\KirimEmailServiceProvider`)
- Laravel Mail Transport (`KirimEmail\Smtp\Laravel\KirimEmailTransport`)
- Symfony Mailer integration for Laravel mail system
- Auto-discovery support for Laravel packages
- Helper functions (`kirimemail_client()`, `kirimemail_messages()`)
- Config file publishing support

### Changed
- Added `symfony/mailer` and `league/html-to-markdown` as dependencies

## [1.3.0] - 2026-03-29

### Added
- `event_type` filter support for log retrieval (`event_type` parameter in getLogs, streamLogs)
- `tags` filter support for log retrieval (`tags` parameter in getLogs, streamLogs)
- `getLogsByEventType()` method to LogsApi for filtering logs by event type
- `getLogsByTags()` method to LogsApi for filtering logs by tags
- Validation for `event_type` parameter against valid LogEntry event type constants
- Event type constants documentation in LogEntry model

### Changed
- Updated LogsApi method docblocks to include new filter parameters

## [1.0.0] - 2025-01-01

### Added
- Initial release
- Full API coverage for domains, credentials, messages, logs, and suppressions
- File upload support with validation
- Real-time log streaming via Server-Sent Events
- Comprehensive error handling with specific exception types
- Rich data models and pagination support
