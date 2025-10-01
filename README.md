# KirimEmail SMTP PHP SDK

A comprehensive PHP SDK for integrating with the KirimEmail SMTP API. This SDK provides a clean, object-oriented interface for managing domains, credentials, sending emails, retrieving logs, and managing suppressions.

## Features

- **Domain Management**: Create, update, verify, and manage email domains
- **Credential Management**: Create and manage SMTP credentials with password generation
- **Email Sending**: Send transactional emails with attachments and template support
- **Log Retrieval**: Access email delivery logs with filtering and real-time streaming
- **Suppression Management**: Handle unsubscribes, bounces, and whitelists
- **Advanced Features**: File uploads, bulk sending, and authentication options

## Requirements

- PHP 7.4 or higher (PHP 8.0+ recommended)
- Guzzle HTTP Client 7.0+
- JSON PHP Extension

## Installation

Install the SDK using Composer:

```bash
composer require kirimemail/smtp-sdk
```

## Quick Start

```php
<?php

require_once 'vendor/autoload.php';

use KirimEmail\Smtp\Client\SmtpClient;
use KirimEmail\Smtp\Api\DomainsApi;
use KirimEmail\Smtp\Api\CredentialsApi;
use KirimEmail\Smtp\Api\MessagesApi;

// Initialize the client
$client = new SmtpClient('your_username', 'your_token');

// List your domains
$domainsApi = new DomainsApi($client);
$domains = $domainsApi->listDomains(['limit' => 10]);

foreach ($domains['data'] as $domain) {
    echo "Domain: " . $domain->getDomain() . "\n";
}

// Send an email
$messagesApi = new MessagesApi($client);
$result = $messagesApi->sendMessage('example.com', [
    'from' => 'sender@example.com',
    'to' => 'recipient@example.com',
    'subject' => 'Hello from KirimEmail SDK',
    'text' => 'This is a test email sent using the PHP SDK.'
]);

if ($result['success']) {
    echo "Email sent successfully!\n";
}
```

## Authentication

The SDK supports two authentication methods:

### Basic Authentication (Username/Token)
Used for user-specific endpoints:

```php
$client = new SmtpClient('your_username', 'your_token');
```

### Domain API Key Authentication
Used for domain-specific endpoints:

```php
$client = new SmtpClient(null, null, 'your_domain_api_key', 'your_domain_api_secret');
```

## API Reference

### Domains API

Manage email domains and their configuration.

```php
use KirimEmail\Smtp\Api\DomainsApi;

$domainsApi = new DomainsApi($client);

// List domains
$domains = $domainsApi->listDomains(['limit' => 10, 'page' => 1]);

// Create a new domain
$result = $domainsApi->createDomain('example.com', 2048);

// Get domain details
$domain = $domainsApi->getDomain('example.com');

// Update domain configuration
$domainsApi->updateDomain('example.com', [
    'open_track' => true,
    'click_track' => true,
    'unsub_track' => true
]);

// Delete a domain
$domainsApi->deleteDomain('example.com');

// Setup authentication domain
$domainsApi->setupAuthDomain('example.com', [
    'auth_domain' => 'auth.example.com',
    'dkim_key_length' => 2048
]);

// Verify DNS records
$domainsApi->verifyMandatoryRecords('example.com');
$domainsApi->verifyAuthDomainRecords('example.com');
```

### Credentials API

Manage SMTP credentials for your domains.

```php
use KirimEmail\Smtp\Api\CredentialsApi;

$credentialsApi = new CredentialsApi($client);

// List credentials for a domain
$credentials = $credentialsApi->listCredentials('example.com');

// Create a new credential
$result = $credentialsApi->createCredential('example.com', 'smtp_user');
echo "Generated password: " . $result['data']['password'];

// Get credential details
$credential = $credentialsApi->getCredential('example.com', 'credential_id');

// Reset credential password
$result = $credentialsApi->resetPassword('example.com', 'credential_id');
echo "New password: " . $result['data']['new_password'];

// Delete a credential
$credentialsApi->deleteCredential('example.com', 'credential_id');
```

### Messages API

Send transactional emails with advanced features.

```php
use KirimEmail\Smtp\Api\MessagesApi;

$messagesApi = new MessagesApi($client);

// Send a simple email
$result = $messagesApi->sendMessage('example.com', [
    'from' => 'sender@example.com',
    'to' => 'recipient@example.com',
    'subject' => 'Test Email',
    'text' => 'Hello World!'
]);

// Send email with attachments
$result = $messagesApi->sendMessage('example.com', [
    'from' => 'sender@example.com',
    'to' => 'recipient@example.com',
    'subject' => 'Email with Attachment',
    'text' => 'Please see the attached file.'
], ['/path/to/file.pdf', '/path/to/image.jpg']);

// Send bulk email
$result = $messagesApi->sendBulkMessage('example.com', [
    'from' => 'sender@example.com',
    'to' => ['user1@example.com', 'user2@example.com'],
    'subject' => 'Bulk Email',
    'text' => 'Hello everyone!'
]);

// Send email with custom headers
$result = $messagesApi->sendMessage('example.com', [
    'from' => 'sender@example.com',
    'to' => 'recipient@example.com',
    'subject' => 'Email with Headers',
    'text' => 'Custom headers included.',
    'headers' => [
        'X-Campaign-ID' => 'welcome-series',
        'X-Priority' => '1',
        'List-Unsubscribe' => 'mailto:unsubscribe@example.com'
    ],
    'reply_to' => 'support@example.com'
]);

// Send template-based email
$result = $messagesApi->sendTemplateMessage('example.com', [
    'template_guid' => '550e8400-e29b-41d4-a716-446655440000',
    'to' => 'recipient@example.com',
    'variables' => [
        'name' => 'John Doe',
        'product' => 'Premium Widget'
    ]
]);

// Send with attachment processing options
$result = $messagesApi->sendMessageWithAttachmentOptions('example.com', [
    'from' => 'sender@example.com',
    'to' => 'recipient@example.com',
    'subject' => 'Secure Document',
    'text' => 'Please find the protected document attached.'
], ['/path/to/secret.pdf'], [
    'compress' => true,
    'password' => 'secure123',
    'watermark' => [
        'enabled' => true,
        'text' => 'CONFIDENTIAL',
        'position' => 'center'
    ]
]);
```

### Logs API

Retrieve and analyze email delivery logs.

```php
use KirimEmail\Smtp\Api\LogsApi;

$logsApi = new LogsApi($client);

// Get recent logs
$logs = $logsApi->getLogs('example.com', ['limit' => 50]);

// Get logs by date range
$startDate = new \DateTime('-7 days');
$endDate = new \DateTime();
$logs = $logsApi->getLogsByDateRange('example.com', $startDate, $endDate);

// Filter by sender or recipient
$logs = $logsApi->getLogsBySender('example.com', 'sender@example.com');
$logs = $logsApi->getLogsByRecipient('example.com', 'recipient@example.com');

// Get logs for a specific message
$logs = $logsApi->getMessageLogs('example.com', 'message-guid-here');

// Get delivery statistics
$stats = $logsApi->getDeliveryStats('example.com');
echo "Delivered: {$stats['delivered']}, Bounced: {$stats['bounced']}";

// Stream logs in real-time (Server-Sent Events)
foreach ($logsApi->streamLogs('example.com', ['limit' => 1000]) as $log) {
    echo "Event: " . $log->getEventType() . " at " . $log->getTimestampDateTime()->format('Y-m-d H:i:s') . "\n";
}
```

### Suppressions API

Manage email suppressions to maintain sending reputation.

```php
use KirimEmail\Smtp\Api\SuppressionsApi;

$suppressionsApi = new SuppressionsApi($client);

// Get all suppressions
$suppressions = $suppressionsApi->getSuppressions('example.com');

// Get suppressions by type
$unsubscribes = $suppressionsApi->getUnsubscribeSuppressions('example.com');
$bounces = $suppressionsApi->getBounceSuppressions('example.com');
$whitelists = $suppressionsApi->getWhitelistSuppressions('example.com');

// Search suppressions
$results = $suppressionsApi->searchSuppressions('example.com', 'user@example.com');

// Check if an email is suppressed
$status = $suppressionsApi->isSuppressed('example.com', 'user@example.com');
if ($status['suppressed']) {
    echo "Email is suppressed with type: " . $status['type'];
}

// Get suppression statistics
$stats = $suppressionsApi->getSuppressionStats('example.com');
echo "Total suppressions: {$stats['total']}";
```

## Data Models

The SDK provides rich data models for API responses:

### Domain Model
```php
$domain = new Domain($data);
echo $domain->getDomain();           // example.com
echo $domain->isVerified();         // true/false
echo $domain->getAuthDomain();      // auth.example.com
echo $domain->getCreatedAtDateTime()->format('Y-m-d H:i:s');
```

### Credential Model
```php
$credential = new Credential($data);
echo $credential->getUsername();     // smtp_user
echo $credential->getUserSmtpGuid(); // smtp-guid-123
echo $credential->getCreatedAtDateTime()->format('Y-m-d H:i:s');
```

### LogEntry Model
```php
$log = new LogEntry($data);
echo $log->getEventType();           // delivered, opened, clicked, etc.
echo $log->getMessageGuid();         // message GUID
echo $log->isDelivered();            // true/false
echo $log->isOpened();               // true/false
echo $log->getTimestampDateTime()->format('Y-m-d H:i:s');
```

### Suppression Model
```php
$suppression = new Suppression($data);
echo $suppression->getType();         // unsubscribe, bounce, whitelist
echo $suppression->getRecipient();    // user@example.com
echo $suppression->isUnsubscribe();   // true/false
echo $suppression->isBounce();        // true/false
echo $suppression->getSource();        // manual, auto, api
```

### Pagination Model
```php
$pagination = new Pagination($data);
echo $pagination->getTotal();        // Total number of items
echo $pagination->getPage();         // Current page
echo $pagination->getLimit();        // Items per page
echo $pagination->getTotalPages();   // Total pages
echo $pagination->hasNextPage();     // true/false
echo $pagination->getNextPage();     // Next page number
```

## Error Handling

The SDK provides comprehensive error handling:

```php
use KirimEmail\Smtp\Exception\ApiException;
use KirimEmail\Smtp\Exception\AuthenticationException;
use KirimEmail\Smtp\Exception\ValidationException;
use KirimEmail\Smtp\Exception\NotFoundException;
use KirimEmail\Smtp\Exception\ServerException;

try {
    $result = $messagesApi->sendMessage('example.com', $data);
} catch (AuthenticationException $e) {
    // Handle authentication failures
    echo "Authentication failed: " . $e->getMessage();
} catch (ValidationException $e) {
    // Handle validation errors
    echo "Validation failed: " . $e->getMessage();
    if ($e->hasErrors()) {
        print_r($e->getErrors());
    }
} catch (NotFoundException $e) {
    // Handle 404 errors
    echo "Resource not found: " . $e->getMessage();
} catch (ServerException $e) {
    // Handle server errors (5xx)
    echo "Server error: " . $e->getMessage();
} catch (ApiException $e) {
    // Handle other API errors
    echo "API error: " . $e->getMessage();
}
```

## File Uploads

The SDK supports file attachments with built-in validation:

```php
// Supported file formats
$supportedFormats = [
    'PDF', 'DOC', 'DOCX', 'XLS', 'XLSX', 'PPT', 'PPTX',
    'TXT', 'CSV', 'JPG', 'JPEG', 'PNG', 'GIF', 'WEBP',
    'ZIP', 'RAR', '7Z'
];

// File size limits
$maxFileSize = 20 * 1024 * 1024; // 20MB per file
$maxTotalSize = 25 * 1024 * 1024; // 25MB total payload
$maxFiles = 10; // Maximum files per email

// Upload with validation
$files = ['/path/to/document.pdf', '/path/to/image.jpg'];
$result = $messagesApi->sendMessage('example.com', $emailData, $files);
```

## Advanced Features

### Bulk Email Sending
```php
// Send to multiple recipients efficiently
$recipients = ['user1@example.com', 'user2@example.com', 'user3@example.com'];
$result = $messagesApi->sendBulkMessage('example.com', [
    'from' => 'sender@example.com',
    'to' => $recipients,
    'subject' => 'Bulk Announcement',
    'text' => 'Important announcement for all recipients.'
]);
```

### Template Variable Replacement
```php
// Use variables in email templates
$result = $messagesApi->sendTemplateMessage('example.com', [
    'template_guid' => 'template-guid-here',
    'to' => 'user@example.com',
    'variables' => [
        'name' => 'John Doe',
        'order_id' => '12345',
        'product' => 'Premium Widget',
        'shipping_date' => '2023-12-25'
    ]
]);
```

### Real-time Log Streaming
```php
// Stream logs for monitoring
foreach ($logsApi->streamLogs('example.com', [
    'start' => (new \DateTime('-1 hour'))->format('c'),
    'limit' => 5000
]) as $log) {
    if ($log->isBounced()) {
        // Handle bounce in real-time
        echo "Bounce detected for: " . $log->getMessageGuid();
    }
}
```

## Configuration

### Custom Base URL
```php
$client = new SmtpClient('username', 'token');
$client->setBaseUrl('https://your-custom-domain.com');
```

### Timeout Configuration
```php
$client = new SmtpClient('username', 'token');
// Default timeout is 30 seconds, connection timeout is 10 seconds
```

## Testing

Run the test suite:

```bash
composer test
```

Run code style checks:

```bash
composer cs-check
```

Fix code style issues:

```bash
composer cs-fix
```

## Examples

Complete usage examples are available in the `examples/` directory:

```bash
php examples/usage.php
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Run the test suite
6. Submit a pull request

## License

This project is licensed under the MIT License. See the LICENSE file for details.

## Support

- **Documentation**: [API Documentation](https://docs.kirim.email)
- **Issues**: [GitHub Issues](https://github.com/kirimemail/kirimemail-smtp-php-sdk/issues)
- **Email**: support@kirim.email
- **Website**: https://kirim.email

## Changelog

### Version 1.0.0
- Initial release
- Full API coverage for domains, credentials, messages, logs, and suppressions
- File upload support with validation
- Real-time log streaming
- Comprehensive error handling
- Rich data models and pagination support