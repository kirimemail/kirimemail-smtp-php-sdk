<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Api;

use KirimEmail\Smtp\Client\SmtpClient;
use KirimEmail\Smtp\Exception\ApiException;

class MessagesApi
{
    private SmtpClient $client;

    public function __construct(SmtpClient $client)
    {
        $this->client = $client;
    }

/**
 * Send transactional email
 *
 * @param string $domain Domain name
 * @param array $data Email data
 * @param array $files Optional file attachments
 * @return array{success: bool, message: string}
 * @throws ApiException
 */
public function sendMessage(string $domain, array $data, array $files = []): array
{
    $this->validateEmailData($data);

    if (!empty($files)) {
        return $this->client->postMultipart("/api/domains/{$domain}/message", $data, $files);
    }

    return $this->client->post("/api/domains/{$domain}/message", $data);
}

/**
 * Send template-based email
 *
 * @param string $domain Domain name
 * @param array $data Template data
 * @param array $files Optional file attachments
 * @return array{success: bool, message: string, template_guid: string, template_name: string}
 * @throws ApiException
 */
public function sendTemplateMessage(string $domain, array $data, array $files = []): array
{
    $this->validateTemplateData($data);

    if (!empty($files)) {
        return $this->client->postMultipart("/api/domains/{$domain}/message/template", $data, $files);
    }

    return $this->client->post("/api/domains/{$domain}/message/template", $data);
}

/**
 * Validate email data
 *
 * @param array $data
 * @throws ApiException
 */
private function validateEmailData(array $data): void
{
    $required = ['from', 'to', 'subject', 'text'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new ApiException("Missing required field: {$field}");
        }
    }

    // Validate email addresses
    if (!$this->isValidEmail($data['from'])) {
        throw new ApiException("Invalid from email address: {$data['from']}");
    }

    if (is_array($data['to'])) {
        foreach ($data['to'] as $email) {
            if (!$this->isValidEmail($email)) {
                throw new ApiException("Invalid to email address: {$email}");
            }
        }
    } else {
        if (!$this->isValidEmail($data['to'])) {
            throw new ApiException("Invalid to email address: {$data['to']}");
        }
    }

    // Validate reply_to if provided
    if (!empty($data['reply_to']) && !$this->isValidEmail($data['reply_to'])) {
        throw new ApiException("Invalid reply_to email address: {$data['reply_to']}");
    }

    // Validate from_name if provided (should be a string)
    if (!empty($data['from_name']) && !is_string($data['from_name'])) {
        throw new ApiException("Invalid from_name: must be a string");
    }
}

/**
 * Validate template data
 *
 * @param array $data
 * @throws ApiException
 */
private function validateTemplateData(array $data): void
{
    if (empty($data['template_guid'])) {
        throw new ApiException("Missing required field: template_guid");
    }

    if (empty($data['to'])) {
        throw new ApiException("Missing required field: to");
    }

    // Validate email addresses
    if (is_array($data['to'])) {
        foreach ($data['to'] as $email) {
            if (!$this->isValidEmail($email)) {
                throw new ApiException("Invalid to email address: {$email}");
            }
        }
    } else {
        if (!$this->isValidEmail($data['to'])) {
            throw new ApiException("Invalid to email address: {$data['to']}");
        }
    }

    // Validate from if provided
    if (!empty($data['from']) && !$this->isValidEmail($data['from'])) {
        throw new ApiException("Invalid from email address: {$data['from']}");
    }

    // Validate reply_to if provided
    if (!empty($data['reply_to']) && !$this->isValidEmail($data['reply_to'])) {
        throw new ApiException("Invalid reply_to email address: {$data['reply_to']}");
    }

    // Validate from_name if provided (should be a string)
    if (!empty($data['from_name']) && !is_string($data['from_name'])) {
        throw new ApiException("Invalid from_name: must be a string");
    }
}

    /**
     * Validate email address
     *
     * @param string $email
     * @return bool
     */
    private function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Send email with attachment options
     *
     * @param string $domain Domain name
     * @param array $data Email data
     * @param array $files File attachments
     * @param array $attachmentOptions Attachment processing options
     * @return array{success: bool, message: string}
     * @throws ApiException
     */
    public function sendMessageWithAttachmentOptions(string $domain, array $data, array $files, array $attachmentOptions = []): array
    {
        if (!empty($attachmentOptions)) {
            $data['attachment_options'] = json_encode($attachmentOptions);
        }

        return $this->sendMessage($domain, $data, $files);
    }

    /**
     * Send bulk email to multiple recipients
     *
     * @param string $domain Domain name
     * @param array $data Email data
     * @param array $files Optional file attachments
     * @return array{success: bool, message: string}
     * @throws ApiException
     */
    public function sendBulkMessage(string $domain, array $data, array $files = []): array
    {
        // Ensure 'to' field is an array for bulk sending
        if (isset($data['to']) && !is_array($data['to'])) {
            throw new ApiException("Bulk email requires 'to' field to be an array of email addresses");
        }

        if (count($data['to']) > 1000) {
            throw new ApiException("Maximum 1000 recipients allowed per request");
        }

        return $this->sendMessage($domain, $data, $files);
    }
}