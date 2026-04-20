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
- **For Laravel integration**: Laravel 7.0+, Symfony Mailer 5.0+, League HTML-to-Markdown 5.0+

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

## Laravel Integration

The SDK includes a Laravel Service Provider for seamless integration with Laravel's mail system.

### Installation

The service provider is automatically discovered by Laravel when you install the package via Composer:

```bash
composer require kirimemail/smtp-sdk
```

### Configuration

1. **Publish the configuration file**:

```bash
php artisan vendor:publish --tag=kirimemail-config
```

This will create `config/kirimemail.php` with the following options:

```php
<?php

return [
    'username' => env('KIRIMEMAIL_USERNAME'),
    'token' => env('KIRIMEMAIL_TOKEN'),
    'domain_api_key' => env('KIRIMEMAIL_DOMAIN_API_KEY'),
    'domain_api_secret' => env('KIRIMEMAIL_DOMAIN_API_SECRET'),
    'domain' => env('KIRIMEMAIL_DOMAIN'),
    'base_url' => env('KIRIMEMAIL_BASE_URL', 'https://smtp-app.kirim.email'),
];
```

2. **Add environment variables to your `.env` file**:

```env
KIRIMEMAIL_USERNAME=your_username
KIRIMEMAIL_TOKEN=your_token
KIRIMEMAIL_DOMAIN=your_domain.com
```

3. **Configure Laravel mail driver** in `config/mail.php`:

```php
<?php

return [
    // ... other mail config

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            // ... your existing SMTP config
        ],

        // Add the KirimEmail mailer
        'kirimemail' => [
            'transport' => 'kirimemail',
        ],
    ],

    'from' => [
        'address' => 'noreply@example.com',
        'name' => 'Your Application Name',
    ],
];
```

4. **Set the default mailer** (optional) in `config/mail.php`:

```php
'default' => env('MAIL_MAILER', 'kirimemail'),
```

Or per-environment in `.env`:

```env
MAIL_MAILER=kirimemail
```

### Usage

#### Using the Mail Facade

```php
use Illuminate\Support\Facades\Mail;

// Simple email
Mail::to('recipient@example.com')
    ->send(new \App\Mail\TestEmail());

// With data
Mail::to('recipient@example.com')
    ->cc('cc@example.com')
    ->bcc('bcc@example.com')
    ->send(new \App\Mail\OrderShipped($order));

// Using the raw method
Mail::raw('Hello World', function ($message) {
    $message->to('recipient@example.com')
            ->subject('Test Subject');
});

// Using the html method
Mail::html('<h1>Hello World</h1><p>This is an HTML email.</p>', function ($message) {
    $message->to('recipient@example.com')
            ->subject('HTML Email Test');
});
```

#### Using Helper Functions

The SDK provides helper functions for direct access to the client and API:

```php
// Get the SMTP client
$client = kirimemail_client();

// Get the messages API
$messagesApi = kirimemail_messages();

// Send email directly
$result = kirimemail_messages()->sendMessage('example.com', [
    'from' => 'sender@example.com',
    'to' => 'recipient@example.com',
    'subject' => 'Hello from KirimEmail',
    'text' => 'This is a test email.',
    'html' => '<h1>Hello</h1><p>This is an HTML email.</p>',
]);
```

### Creating Mailable Classes

```php
<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Mail\Mailable as MailableContract;

class WelcomeEmail extends Mailable implements MailableContract
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Welcome to Our Application')
                    ->view('emails.welcome')
                    ->with([
                        'name' => $this->user->name,
                        'email' => $this->user->email,
                    ]);
    }
}
```

### Sending with Attachments

```php
use Illuminate\Support\Facades\Mail;

Mail::to('recipient@example.com')
    ->send(new \App\Mail\InvoiceEmail($invoice));

// Or with inline attachments
Mail::to('recipient@example.com')
    ->send(new \App\Mail\ReportEmail($report)
        ->attach(storage_path('app/reports/monthly.pdf'))
    );
```

### Error Handling

```php
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;

try {
    Mail::to('recipient@example.com')
        ->send(new \App\Mail\TestEmail());
} catch (TransportException $e) {
    Log::error('KirimEmail error: ' . $e->getMessage());
}
```

### Manual Service Provider Registration

If auto-discovery doesn't work, manually register the service provider in `config/app.php`:

```php
'providers' => [
    // ... other providers

    KirimEmail\Smtp\Laravel\KirimEmailServiceProvider::class,
],
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

// Filter by event type (queued, delivered, bounced, failed, opened, clicked, unsubscribed, etc.)
use KirimEmail\Smtp\Model\LogEntry;
$logs = $logsApi->getLogsByEventType('example.com', LogEntry::SMTP_EVENT_DELIVERED);

// Filter by tags (partial match)
$logs = $logsApi->getLogsByTags('example.com', 'newsletter');

// Combine filters with additional parameters
$logs = $logsApi->getLogs('example.com', [
    'event_type' => 'bounced',
    'tags' => 'marketing',
    'start' => (new \DateTime('-30 days'))->format('c'),
    'limit' => 100
]);

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

// Delete suppressions by IDs
$result = $suppressionsApi->deleteUnsubscribeSuppressions('example.com', [1, 2, 3]);
echo "Deleted {$result['deleted_count']} suppressions.";

// Create whitelist suppression
$result = $suppressionsApi->createWhitelistSuppression('example.com', 'trusted@sender.com', 'email', 'Trusted partner');
```

### Email Validation API

Validate email addresses with comprehensive checks including RFC compliance, DNS verification, and spamtrap detection.

```php
use KirimEmail\Smtp\Api\EmailValidationApi;

$emailValidationApi = new EmailValidationApi($client);

// Validate a single email
$result = $emailValidationApi->validate('user@example.com');
echo "Is valid: " . ($result['data']->isValid() ? 'Yes' : 'No');
if (!$result['data']->isCached()) {
    echo "Validated at: " . $result['data']->getValidatedAt();
}

// Validate with strict mode
$result = $emailValidationApi->validateStrict('user@example.com');
echo "Strict validation result: " . ($result['data']->isValid() ? 'Pass' : 'Fail');

// Validate multiple emails
$emails = ['user1@example.com', 'user2@example.com', 'user3@example.com'];
$result = $emailValidationApi->validateBatch($emails);
echo "Valid: {$result['data']->getValidCount()}, Invalid: {$result['data']->getInvalidCount()}";
echo "Cached: {$result['data']->getCachedCount()}, Freshly validated: {$result['data']->getValidatedCount()}";

// Validate batch with strict mode
$result = $emailValidationApi->validateBatchStrict($emails);
echo "Strict batch validation completed.";
```

### User API

Get user quota information and usage statistics.

```php
use KirimEmail\Smtp\Api\UserApi;

$userApi = new UserApi($client);

// Get user quota
$quota = $userApi->getQuota();
echo "Current quota: {$quota['data']['current_quota']}";
echo "Max quota: {$quota['data']['max_quota']}";
echo "Usage percentage: {$quota['data']['usage_percentage']}%";
```

### Webhooks API

Manage webhook configurations for real-time email event notifications.

```php
use KirimEmail\Smtp\Api\WebhooksApi;

$webhooksApi = new WebhooksApi($client);

// List webhooks
$webhooks = $webhooksApi->getWebhooks('example.com');
foreach ($webhooks['data'] as $webhook) {
    echo "Webhook: {$webhook->getType()} -> {$webhook->getUrl()}\n";
}

// Create a new webhook
$result = $webhooksApi->createWebhook('example.com', 'delivered', 'https://example.com/webhook');
echo "Created webhook with GUID: {$result['data']->getWebhookGuid()}";

// Get specific webhook
$webhook = $webhooksApi->getWebhook('example.com', 'webhook-guid-here');
echo "Webhook type: {$webhook->getType()}, URL: {$webhook->getUrl()}";

// Update webhook
$webhook = $webhooksApi->updateWebhook('example.com', 'webhook-guid-here', [
    'type' => 'opened',
    'url' => 'https://example.com/new-webhook'
]);
echo "Webhook updated successfully.";

// Delete webhook
$result = $webhooksApi->deleteWebhook('example.com', 'webhook-guid-here');
echo "Webhook deleted successfully.";

// Test webhook URL
$result = $webhooksApi->testWebhook('example.com', 'https://example.com/webhook', 'delivered');
if ($result['success']) {
    echo "Webhook test successful. Response status: {$result['data']['response_status']}";
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

// Event type constants
LogEntry::SMTP_EVENT_QUEUED;         // 'queued'
LogEntry::SMTP_EVENT_SEND;           // 'send'
LogEntry::SMTP_EVENT_DELIVERED;      // 'delivered'
LogEntry::SMTP_EVENT_BOUNCED;        // 'bounced'
LogEntry::SMTP_EVENT_FAILED;         // 'failed'
LogEntry::SMTP_EVENT_PERMANENT_FAIL; // 'permanent_fail'
LogEntry::SMTP_EVENT_OPENED;         // 'opened'
LogEntry::SMTP_EVENT_CLICKED;        // 'clicked'
LogEntry::SMTP_EVENT_UNSUBSCRIBED;   // 'unsubscribed'
LogEntry::SMTP_EVENT_TEMP_FAILURE;   // 'temp_fail'
LogEntry::SMTP_EVENT_DEFERRED;       // 'deferred'
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
