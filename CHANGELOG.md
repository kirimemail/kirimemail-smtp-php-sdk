# Changelog

All notable changes to this project will be documented in this file.

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
