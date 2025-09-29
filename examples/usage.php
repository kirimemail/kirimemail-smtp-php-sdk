<?php

require_once __DIR__ . '/../vendor/autoload.php';

use KirimEmail\Smtp\Client\SmtpClient;
use KirimEmail\Smtp\Api\DomainsApi;
use KirimEmail\Smtp\Api\CredentialsApi;
use KirimEmail\Smtp\Api\MessagesApi;
use KirimEmail\Smtp\Api\LogsApi;
use KirimEmail\Smtp\Api\SuppressionsApi;
use KirimEmail\Smtp\Exception\ApiException;
use KirimEmail\Smtp\Exception\AuthenticationException;
use KirimEmail\Smtp\Exception\ValidationException;

// Example configuration
$username = 'your_username';
$token = 'your_token';
$domain = 'example.com';

try {
    // Initialize the client
    $client = new SmtpClient($username, $token);

    // Initialize API classes
    $domainsApi = new DomainsApi($client);
    $credentialsApi = new CredentialsApi($client);
    $messagesApi = new MessagesApi($client);
    $logsApi = new LogsApi($client);
    $suppressionsApi = new SuppressionsApi($client);

    echo "=== KirimEmail SMTP SDK Usage Examples ===\n\n";

    // === DOMAINS API EXAMPLES ===
    echo "1. DOMAINS API EXAMPLES\n";
    echo "------------------------\n";

    // List domains
    try {
        $domains = $domainsApi->listDomains(['limit' => 5]);
        echo "✓ Listed " . count($domains['data']) . " domains\n";

        if (!empty($domains['data'])) {
            $firstDomain = $domains['data'][0];
            echo "  First domain: " . $firstDomain->getDomain() .
                 " (Verified: " . ($firstDomain->isVerified() ? 'Yes' : 'No') . ")\n";
        }
    } catch (ApiException $e) {
        echo "✗ Failed to list domains: " . $e->getMessage() . "\n";
    }

    // Create a new domain
    try {
        $result = $domainsApi->createDomain('test.example.com', 2048);
        echo "✓ Created domain: " . ($result['success'] ? 'Success' : 'Failed') . "\n";
    } catch (ApiException $e) {
        echo "✗ Failed to create domain: " . $e->getMessage() . "\n";
    }

    // Get domain details
    try {
        $domainInfo = $domainsApi->getDomain($domain);
        echo "✓ Got domain details for: " . $domainInfo['data']->getDomain() . "\n";
    } catch (ApiException $e) {
        echo "✗ Failed to get domain details: " . $e->getMessage() . "\n";
    }

    echo "\n";

    // === CREDENTIALS API EXAMPLES ===
    echo "2. CREDENTIALS API EXAMPLES\n";
    echo "----------------------------\n";

    // List credentials
    try {
        $credentials = $credentialsApi->listCredentials($domain, ['limit' => 5]);
        echo "✓ Listed " . count($credentials['data']) . " credentials for domain: {$domain}\n";

        if (!empty($credentials['data'])) {
            $firstCredential = $credentials['data'][0];
            echo "  First credential: " . $firstCredential->getUsername() . "\n";
        }
    } catch (ApiException $e) {
        echo "✗ Failed to list credentials: " . $e->getMessage() . "\n";
    }

    // Create a new credential
    try {
        $result = $credentialsApi->createCredential($domain, 'test_user_' . time());
        echo "✓ Created credential: " . ($result['success'] ? 'Success' : 'Failed') . "\n";

        if ($result['success'] && isset($result['data']['credential'])) {
            $credential = $result['data']['credential'];
            echo "  Username: " . $credential->getUsername() . "\n";
            echo "  Generated password: " . $credential->getPassword() . "\n";
            echo "  Remote synced: " . ($credential->isRemoteSynced() ? 'Yes' : 'No') . "\n";
            echo "  IMPORTANT: Store this password securely as it cannot be retrieved later!\n";
        }
    } catch (ApiException $e) {
        echo "✗ Failed to create credential: " . $e->getMessage() . "\n";
    }

    echo "\n";

    // === MESSAGES API EXAMPLES ===
    echo "3. MESSAGES API EXAMPLES\n";
    echo "-------------------------\n";

    // Send a simple email
    try {
        $emailData = [
            'from' => 'sender@' . $domain,
            'to' => 'recipient@example.com',
            'subject' => 'Test Email from PHP SDK',
            'text' => "Hello!\n\nThis is a test email sent using the KirimEmail PHP SDK.\n\nBest regards,\nPHP SDK"
        ];

        $result = $messagesApi->sendMessage($domain, $emailData);
        echo "✓ Sent email: " . ($result['success'] ? 'Success' : 'Failed') . "\n";
        echo "  Message: " . ($result['message'] ?? 'No message') . "\n";
    } catch (ApiException $e) {
        echo "✗ Failed to send email: " . $e->getMessage() . "\n";
    }

    // Send email with attachment
    try {
        $emailData = [
            'from' => 'sender@' . $domain,
            'to' => 'recipient@example.com',
            'subject' => 'Email with Attachment',
            'text' => "Please see the attached file."
        ];

        // Note: Replace with actual file path
        $files = [__FILE__]; // Attach this example file

        $result = $messagesApi->sendMessage($domain, $emailData, $files);
        echo "✓ Sent email with attachment: " . ($result['success'] ? 'Success' : 'Failed') . "\n";
    } catch (ApiException $e) {
        echo "✗ Failed to send email with attachment: " . $e->getMessage() . "\n";
    }

    // Send bulk email
    try {
        $emailData = [
            'from' => 'sender@' . $domain,
            'to' => ['recipient1@example.com', 'recipient2@example.com'],
            'subject' => 'Bulk Email from PHP SDK',
            'text' => "This is a bulk email sent to multiple recipients."
        ];

        $result = $messagesApi->sendBulkMessage($domain, $emailData);
        echo "✓ Sent bulk email: " . ($result['success'] ? 'Success' : 'Failed') . "\n";
        echo "  Message: " . ($result['message'] ?? 'No message') . "\n";
    } catch (ApiException $e) {
        echo "✗ Failed to send bulk email: " . $e->getMessage() . "\n";
    }

    // Send template-based email (if you have template GUID)
    try {
        $templateData = [
            'template_guid' => '550e8400-e29b-41d4-a716-446655440000', // Replace with actual template GUID
            'to' => 'recipient@example.com',
            'variables' => [
                'name' => 'John Doe',
                'product' => 'PHP SDK'
            ]
        ];

        $result = $messagesApi->sendTemplateMessage($domain, $templateData);
        echo "✓ Sent template email: " . ($result['success'] ? 'Success' : 'Failed') . "\n";
    } catch (ApiException $e) {
        echo "✗ Failed to send template email: " . $e->getMessage() . "\n";
    }

    echo "\n";

    // === LOGS API EXAMPLES ===
    echo "4. LOGS API EXAMPLES\n";
    echo "---------------------\n";

    // Get recent logs
    try {
        $logs = $logsApi->getLogs($domain, ['limit' => 10]);
        echo "✓ Retrieved " . count($logs['data']) . " log entries\n";

        if (!empty($logs['data'])) {
            $firstLog = $logs['data'][0];
            echo "  Latest event: " . $firstLog->getEventType() .
                 " at " . ($firstLog->getTimestampDateTime()?->format('Y-m-d H:i:s')) . "\n";
        }
    } catch (ApiException $e) {
        echo "✗ Failed to get logs: " . $e->getMessage() . "\n";
    }

    // Get logs by date range
    try {
        $startDate = new \DateTime('-7 days');
        $endDate = new \DateTime();

        $logs = $logsApi->getLogsByDateRange($domain, $startDate, $endDate, ['limit' => 5]);
        echo "✓ Retrieved " . count($logs['data']) . " logs from last 7 days\n";
    } catch (ApiException $e) {
        echo "✗ Failed to get logs by date range: " . $e->getMessage() . "\n";
    }

    // Stream logs (commented out as it's a long-running operation)
    /*
    echo "Starting log streaming (press Ctrl+C to stop)...\n";
    foreach ($logsApi->streamLogs($domain, ['limit' => 100]) as $log) {
        echo "Streamed event: " . $log->getEventType() .
             " at " . ($log->getTimestampDateTime()?->format('Y-m-d H:i:s')) . "\n";
    }
    */

    echo "\n";

    // === SUPPRESSIONS API EXAMPLES ===
    echo "5. SUPPRESSIONS API EXAMPLES\n";
    echo "------------------------------\n";

    // Get suppressions
    try {
        $suppressions = $suppressionsApi->getSuppressions($domain, ['limit' => 5]);
        echo "✓ Retrieved " . count($suppressions['data']) . " suppressions\n";

        if (!empty($suppressions['data'])) {
            $firstSuppression = $suppressions['data'][0];
            echo "  Latest suppression: " . $firstSuppression->getType() .
                 " for " . $firstSuppression->getRecipient() . "\n";
        }
    } catch (ApiException $e) {
        echo "✗ Failed to get suppressions: " . $e->getMessage() . "\n";
    }

    echo "\n";

    // === ERROR HANDLING EXAMPLES ===
    echo "6. ERROR HANDLING EXAMPLES\n";
    echo "----------------------------\n";

    // Example of specific exception handling
    try {
        // This will likely fail with invalid credentials
        $invalidClient = new SmtpClient('invalid_user', 'invalid_token');
        $invalidDomainsApi = new DomainsApi($invalidClient);
        $invalidDomainsApi->listDomains();
    } catch (AuthenticationException $e) {
        echo "✓ Caught authentication error: " . $e->getMessage() . "\n";
    } catch (ValidationException $e) {
        echo "✓ Caught validation error: " . $e->getMessage() . "\n";
        if ($e->hasErrors()) {
            echo "  Errors: " . print_r($e->getErrors(), true);
        }
    } catch (ApiException $e) {
        echo "✓ Caught API error: " . $e->getMessage() . "\n";
    }

    echo "\n=== EXAMPLES COMPLETED ===\n";

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}