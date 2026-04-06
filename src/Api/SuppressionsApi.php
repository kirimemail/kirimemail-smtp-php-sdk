<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Api;

use KirimEmail\Smtp\Client\SmtpClient;
use KirimEmail\Smtp\Model\Suppression;
use KirimEmail\Smtp\Model\Pagination;
use KirimEmail\Smtp\Exception\ApiException;

class SuppressionsApi
{
    private SmtpClient $client;

    public function __construct(SmtpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get domain suppressions
     *
     * @param string $domain Domain name
     * @param array $params Query parameters (type, search, page, per_page)
     * @return array{data: Suppression[], pagination: Pagination, filters: array}
     * @throws ApiException
     */
    public function getSuppressions(string $domain, array $params = []): array
    {
        $this->validateSuppressionParams($params);
        $response = $this->client->get("/api/domains/{$domain}/suppressions", $params);

        $suppressions = [];
        foreach ($response['data'] ?? [] as $suppressionData) {
            $suppressions[] = new Suppression($suppressionData);
        }

        $pagination = isset($response['pagination']) ? new Pagination($response['pagination']) : null;

        return [
            'data' => $suppressions,
            'pagination' => $pagination,
            'filters' => $response['filters'] ?? [],
        ];
    }

    /**
     * Get unsubscribe suppressions
     *
     * @param string $domain Domain name
     * @param array $params Query parameters (search, page, per_page)
     * @return array{data: Suppression[], pagination: Pagination, filters: array}
     * @throws ApiException
     */
    public function getUnsubscribeSuppressions(string $domain, array $params = []): array
    {
        return $this->getSuppressionsByType($domain, 'unsubscribe', $params);
    }

    /**
     * Get bounce suppressions
     *
     * @param string $domain Domain name
     * @param array $params Query parameters (search, page, per_page)
     * @return array{data: Suppression[], pagination: Pagination, filters: array}
     * @throws ApiException
     */
    public function getBounceSuppressions(string $domain, array $params = []): array
    {
        return $this->getSuppressionsByType($domain, 'bounce', $params);
    }

    /**
     * Get whitelist suppressions
     *
     * @param string $domain Domain name
     * @param array $params Query parameters (search, page, per_page)
     * @return array{data: Suppression[], pagination: Pagination, filters: array}
     * @throws ApiException
     */
    public function getWhitelistSuppressions(string $domain, array $params = []): array
    {
        return $this->getSuppressionsByType($domain, 'whitelist', $params);
    }

    /**
     * Get suppressions by type
     *
     * @param string $domain Domain name
     * @param string $type Suppression type (unsubscribe, bounce, whitelist)
     * @param array $params Query parameters (search, page, per_page)
     * @return array{data: Suppression[], pagination: Pagination, filters: array}
     * @throws ApiException
     */
    public function getSuppressionsByType(string $domain, string $type, array $params = []): array
    {
        $params['type'] = $type;
        return $this->getSuppressions($domain, $params);
    }

    /**
     * Search suppressions by recipient
     *
     * @param string $domain Domain name
     * @param string $search Search term (email or domain)
     * @param array $additionalParams Additional parameters
     * @return array{data: Suppression[], pagination: Pagination, filters: array}
     * @throws ApiException
     */
    public function searchSuppressions(string $domain, string $search, array $additionalParams = []): array
    {
        $params = array_merge($additionalParams, [
            'search' => $search,
        ]);

        return $this->getSuppressions($domain, $params);
    }

    /**
     * Get suppressions with pagination
     *
     * @param string $domain Domain name
     * @param int $page Page number
     * @param int $perPage Items per page
     * @param array $additionalParams Additional parameters
     * @return array{data: Suppression[], pagination: Pagination, filters: array}
     * @throws ApiException
     */
    public function getSuppressionsPaginated(string $domain, int $page = 1, int $perPage = 10, array $additionalParams = []): array
    {
        $params = array_merge($additionalParams, [
            'page' => $page,
            'per_page' => $perPage,
        ]);

        return $this->getSuppressions($domain, $params);
    }


    /**
     * Get suppressions created after a specific date
     *
     * @param string $domain Domain name
     * @param \DateTimeInterface $startDate Start date
     * @param array $additionalParams Additional parameters
     * @return array{data: Suppression[], pagination: Pagination, filters: array}
     * @throws ApiException
     */
    public function getSuppressionsCreatedAfter(string $domain, \DateTimeInterface $startDate, array $additionalParams = []): array
    {
        // Note: This would require the API to support filtering by creation date
        // For now, we'll get all suppressions and filter client-side
        $allSuppressions = $this->getSuppressions($domain, $additionalParams);

        $filteredSuppressions = array_filter($allSuppressions['data'], function ($suppression) use ($startDate) {
            $createdAt = $suppression->getCreatedAtDateTime();
            return $createdAt && $createdAt >= $startDate;
        });

        return [
            'data' => array_values($filteredSuppressions),
            'pagination' => $allSuppressions['pagination'],
            'filters' => $allSuppressions['filters'],
        ];
    }

    /**
     * Delete unsubscribe suppressions
     *
     * @param string $domain Domain name
     * @param array $ids Array of suppression IDs to delete
     * @return void
     * @throws ApiException
     */
    public function deleteUnsubscribeSuppressions(string $domain, array $ids): void
    {
        $this->deleteSuppressions($domain, 'unsubscribes', $ids);
    }

    /**
     * Delete bounce suppressions
     *
     * @param string $domain Domain name
     * @param array $ids Array of suppression IDs to delete
     * @return void
     * @throws ApiException
     */
    public function deleteBounceSuppressions(string $domain, array $ids): void
    {
        $this->deleteSuppressions($domain, 'bounces', $ids);
    }

    /**
     * Delete whitelist suppressions
     *
     * @param string $domain Domain name
     * @param array $ids Array of suppression IDs to delete
     * @return void
     * @throws ApiException
     */
    public function deleteWhitelistSuppressions(string $domain, array $ids): void
    {
        $this->deleteSuppressions($domain, 'whitelist', $ids);
    }

    /**
     * Create a whitelist suppression
     *
     * @param string $domain Domain name
     * @param string $recipient Email or domain to whitelist
     * @param string $recipientType Type of recipient (email or domain)
     * @param string|null $description Optional description
     * @return array{success: bool, message: string, data: Suppression}
     * @throws ApiException
     */
    public function createWhitelistSuppression(string $domain, string $recipient, string $recipientType, ?string $description = null): array
    {
        $this->validateRecipientType($recipientType);

        $data = [
            'recipient' => $recipient,
            'recipient_type' => $recipientType,
        ];

        if ($description !== null) {
            $data['description'] = $description;
        }

        $response = $this->client->post("/api/domains/{$domain}/suppressions/whitelist", $data);

        return [
            'success' => true,
            'message' => $response['message'] ?? 'Whitelist entry created successfully.',
            'data' => new Suppression($response['data']),
        ];
    }

    /**
     * Delete suppressions by type
     *
     * @param string $domain Domain name
     * @param string $type Suppression type endpoint (unsubscribes, bounces, whitelist)
     * @param array $ids Array of suppression IDs to delete
     * @return void
     * @throws ApiException
     */
    private function deleteSuppressions(string $domain, string $type, array $ids): void
    {
        $this->validateSuppressionIds($ids);

        $data = ['ids' => $ids];
        $this->client->deleteWithBody("/api/domains/{$domain}/suppressions/{$type}", $data);
    }

    /**
     * Validate suppression parameters
     *
     * @param array $params
     * @throws ApiException
     */
    private function validateSuppressionParams(array $params): void
    {
        // Validate type - support both query parameter types and endpoint path segments
        if (isset($params['type'])) {
            $validTypes = ['unsubscribe', 'bounce', 'whitelist', 'unsubscribes', 'bounces'];
            if (!in_array($params['type'], $validTypes)) {
                throw new ApiException("Invalid suppression type. Must be one of: " . implode(', ', $validTypes));
            }
        }

        // Validate pagination parameters
        if (isset($params['page'])) {
            $page = (int) $params['page'];
            if ($page < 1) {
                throw new ApiException("Page must be greater than or equal to 1.");
            }
        }

        if (isset($params['per_page'])) {
            $perPage = (int) $params['per_page'];
            if ($perPage < 10 || $perPage > 100) {
                throw new ApiException("Per page must be between 10 and 100.");
            }
        }
    }

    /**
     * Validate suppression IDs
     *
     * @param array $ids
     * @throws ApiException
     */
    private function validateSuppressionIds(array $ids): void
    {
        if (empty($ids)) {
            throw new ApiException("Suppression IDs array is required");
        }

        foreach ($ids as $id) {
            if (!is_int($id) || $id < 1) {
                throw new ApiException("Invalid suppression ID: {$id}");
            }
        }
    }

    /**
     * Validate recipient type
     *
     * @param string $recipientType
     * @throws ApiException
     */
    private function validateRecipientType(string $recipientType): void
    {
        $validTypes = ['email', 'domain'];

        if (!in_array($recipientType, $validTypes)) {
            throw new ApiException("Invalid recipient type. Must be one of: " . implode(', ', $validTypes));
        }
    }
}
