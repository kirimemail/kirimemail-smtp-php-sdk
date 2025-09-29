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
        return $this->getSuppressionsByType($domain, 'unsubscribes', $params);
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
        return $this->getSuppressionsByType($domain, 'bounces', $params);
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

        $filteredSuppressions = array_filter($allSuppressions['data'], function($suppression) use ($startDate) {
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
     * Validate suppression parameters
     *
     * @param array $params
     * @throws ApiException
     */
    private function validateSuppressionParams(array $params): void
    {
        // Validate type
        if (isset($params['type'])) {
            $validTypes = ['unsubscribe', 'bounce', 'whitelist'];
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
}