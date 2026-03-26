<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Api;

use KirimEmail\Smtp\Client\SmtpClient;
use KirimEmail\Smtp\Exception\ApiException;

class UserApi
{
    private SmtpClient $client;

    public function __construct(SmtpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get user quota information
     *
     * @return array{success: bool, data: array{current_quota: int, max_quota: int, usage_percentage: float}}
     * @throws ApiException
     */
    public function getQuota(): array
    {
        $response = $this->client->get('/api/quota');

        return [
            'success' => true,
            'data' => [
                'current_quota' => $response['data']['current_quota'],
                'max_quota' => $response['data']['max_quota'],
                'usage_percentage' => $response['data']['usage_percentage'],
            ],
        ];
    }
}
