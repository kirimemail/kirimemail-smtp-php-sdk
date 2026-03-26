<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Api;

use KirimEmail\Smtp\Client\SmtpClient;
use KirimEmail\Smtp\Model\EmailValidationResult;
use KirimEmail\Smtp\Model\EmailValidationBatchResult;
use KirimEmail\Smtp\Exception\ApiException;

class EmailValidationApi
{
    private SmtpClient $client;

    public function __construct(SmtpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Validate a single email address
     *
     * @param string $email Email address to validate
     * @return array{success: bool, data: EmailValidationResult}
     * @throws ApiException
     */
    public function validate(string $email): array
    {
        $this->validateEmail($email);

        $data = ['email' => $email];
        $response = $this->client->post('/api/email/validate', $data);

        return [
            'success' => true,
            'data' => new EmailValidationResult($response['data']),
        ];
    }

    /**
     * Validate a single email address with strict mode
     *
     * @param string $email Email address to validate
     * @return array{success: bool, data: EmailValidationResult}
     * @throws ApiException
     */
    public function validateStrict(string $email): array
    {
        $this->validateEmail($email);

        $data = ['email' => $email];
        $response = $this->client->post('/api/email/validate/strict', $data);

        return [
            'success' => true,
            'data' => new EmailValidationResult($response['data']),
        ];
    }

    /**
     * Validate multiple email addresses
     *
     * @param array $emails Array of email addresses to validate (max 100)
     * @return array{success: bool, data: EmailValidationBatchResult}
     * @throws ApiException
     */
    public function validateBatch(array $emails): array
    {
        $this->validateEmailBatch($emails);

        $data = ['emails' => $emails];
        $response = $this->client->post('/api/email/validate/bulk', $data);

        return [
            'success' => true,
            'data' => new EmailValidationBatchResult($response['data']),
        ];
    }

    /**
     * Validate multiple email addresses with strict mode
     *
     * @param array $emails Array of email addresses to validate (max 100)
     * @return array{success: bool, data: EmailValidationBatchResult}
     * @throws ApiException
     */
    public function validateBatchStrict(array $emails): array
    {
        $this->validateEmailBatch($emails);

        $data = ['emails' => $emails];
        $response = $this->client->post('/api/email/validate/bulk/strict', $data);

        return [
            'success' => true,
            'data' => new EmailValidationBatchResult($response['data']),
        ];
    }

    /**
     * Validate email address
     *
     * @param string $email
     * @throws ApiException
     */
    private function validateEmail(string $email): void
    {
        if (empty($email)) {
            throw new ApiException("Email address is required");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new ApiException("Invalid email address: {$email}");
        }

        if (strlen($email) > 254) {
            throw new ApiException("Email address exceeds maximum length of 254 characters");
        }
    }

    /**
     * Validate batch of email addresses
     *
     * @param array $emails
     * @throws ApiException
     */
    private function validateEmailBatch(array $emails): void
    {
        if (empty($emails)) {
            throw new ApiException("Emails array is required");
        }

        if (count($emails) > 100) {
            throw new ApiException("Maximum 100 email addresses allowed per batch");
        }

        foreach ($emails as $email) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new ApiException("Invalid email address in batch: {$email}");
            }

            if (strlen($email) > 254) {
                throw new ApiException("Email address exceeds maximum length of 254 characters: {$email}");
            }
        }
    }
}
