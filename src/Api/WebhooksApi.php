<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Api;

use KirimEmail\Smtp\Client\SmtpClient;
use KirimEmail\Smtp\Model\Webhook;
use KirimEmail\Smtp\Exception\ApiException;

class WebhooksApi
{
    private SmtpClient $client;

    public function __construct(SmtpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get webhooks for a domain
     *
     * @param string $domain Domain name
     * @param array $params Query parameters (type)
     * @return array{success: bool, data: Webhook[], count: int}
     * @throws ApiException
     */
    public function getWebhooks(string $domain, array $params = []): array
    {
        $this->validateWebhookParams($params);
        $response = $this->client->get("/api/domains/{$domain}/webhooks", $params);

        $webhooks = [];
        foreach ($response['data'] ?? [] as $webhookData) {
            $webhooks[] = new Webhook($webhookData);
        }

        return [
            'success' => true,
            'data' => $webhooks,
            'count' => $response['count'] ?? count($webhooks),
        ];
    }

    /**
     * Create a new webhook
     *
     * @param string $domain Domain name
     * @param string $type Event type
     * @param string $url Webhook URL
     * @return array{success: bool, message: string, data: Webhook}
     * @throws ApiException
     */
    public function createWebhook(string $domain, string $type, string $url): array
    {
        $this->validateWebhookType($type);
        $this->validateWebhookUrl($url);

        $data = [
            'type' => $type,
            'url' => $url,
        ];
        $response = $this->client->post("/api/domains/{$domain}/webhooks", $data);

        return [
            'success' => true,
            'message' => $response['message'] ?? 'Webhook created successfully.',
            'data' => new Webhook($response['data']),
        ];
    }

    /**
     * Get a specific webhook
     *
     * @param string $domain Domain name
     * @param string $webhookGuid Webhook GUID
     * @return array{success: bool, data: Webhook}
     * @throws ApiException
     */
    public function getWebhook(string $domain, string $webhookGuid): array
    {
        $response = $this->client->get("/api/domains/{$domain}/webhooks/{$webhookGuid}");

        return [
            'success' => true,
            'data' => new Webhook($response['data']),
        ];
    }

    /**
     * Update a webhook
     *
     * @param string $domain Domain name
     * @param string $webhookGuid Webhook GUID
     * @param array $data Webhook updates (type, url)
     * @return array{success: bool, message: string, data: Webhook}
     * @throws ApiException
     */
    public function updateWebhook(string $domain, string $webhookGuid, array $data): array
    {
        if (isset($data['type'])) {
            $this->validateWebhookType($data['type']);
        }
        if (isset($data['url'])) {
            $this->validateWebhookUrl($data['url']);
        }

        $response = $this->client->put("/api/domains/{$domain}/webhooks/{$webhookGuid}", $data);

        return [
            'success' => true,
            'message' => $response['message'] ?? 'Webhook updated successfully.',
            'data' => new Webhook($response['data']),
        ];
    }

    /**
     * Delete a webhook
     *
     * @param string $domain Domain name
     * @param string $webhookGuid Webhook GUID
     * @return array{success: bool, message: string}
     * @throws ApiException
     */
    public function deleteWebhook(string $domain, string $webhookGuid): array
    {
        return $this->client->delete("/api/domains/{$domain}/webhooks/{$webhookGuid}");
    }

    /**
     * Test a webhook URL
     *
     * @param string $domain Domain name
     * @param string $url Webhook URL to test
     * @param string $eventType Event type to use for test
     * @return array{success: bool, message: string, data: array}
     * @throws ApiException
     */
    public function testWebhook(string $domain, string $url, string $eventType): array
    {
        $this->validateWebhookType($eventType);
        $this->validateWebhookUrl($url);

        $data = [
            'url' => $url,
            'event_type' => $eventType,
        ];
        $response = $this->client->post("/api/domains/{$domain}/webhooks/test", $data);

        return [
            'success' => $response['success'] ?? true,
            'message' => $response['message'] ?? 'Webhook test completed.',
            'data' => $response['data'] ?? [],
        ];
    }

    /**
     * Validate webhook type
     *
     * @param string $type
     * @throws ApiException
     */
    private function validateWebhookType(string $type): void
    {
        $validTypes = [
            'queued',
            'send',
            'delivered',
            'bounced',
            'failed',
            'permanent_fail',
            'opened',
            'clicked',
            'unsubscribed',
            'temporary_fail',
            'deferred'
        ];

        if (!in_array($type, $validTypes)) {
            throw new ApiException("Invalid webhook type. Must be one of: " . implode(', ', $validTypes));
        }
    }

    /**
     * Validate webhook URL
     *
     * @param string $url
     * @throws ApiException
     */
    private function validateWebhookUrl(string $url): void
    {
        if (empty($url)) {
            throw new ApiException("Webhook URL is required");
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new ApiException("Invalid webhook URL: {$url}");
        }

        $parsed = parse_url($url);
        if (!in_array($parsed['scheme'] ?? '', ['http', 'https'])) {
            throw new ApiException("Webhook URL must use HTTP or HTTPS protocol");
        }
    }

    /**
     * Validate webhook parameters
     *
     * @param array $params
     * @throws ApiException
     */
    private function validateWebhookParams(array $params): void
    {
        if (isset($params['type'])) {
            $this->validateWebhookType($params['type']);
        }
    }
}
