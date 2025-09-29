<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Api;

use KirimEmail\Smtp\Client\SmtpClient;
use KirimEmail\Smtp\Model\Domain;
use KirimEmail\Smtp\Model\Pagination;
use KirimEmail\Smtp\Exception\ApiException;

class DomainsApi
{
    private SmtpClient $client;

    public function __construct(SmtpClient $client)
    {
        $this->client = $client;
    }

    /**
     * List user domains
     *
     * @param array $params Query parameters (limit, page, search)
     * @return array{data: Domain[], pagination: Pagination}
     * @throws ApiException
     */
    public function listDomains(array $params = []): array
    {
        $response = $this->client->get('/api/domains', $params);

        $domains = [];
        foreach ($response['data'] ?? [] as $domainData) {
            $domains[] = new Domain($domainData);
        }

        $pagination = isset($response['pagination']) ? new Pagination($response['pagination']) : null;

        return [
            'data' => $domains,
            'pagination' => $pagination,
        ];
    }

    /**
     * Create a new domain
     *
     * @param string $domain Domain name
     * @param int $dkimKeyLength DKIM key length (1024 or 2048)
     * @return array{success: bool, message: string, data: array}
     * @throws ApiException
     */
    public function createDomain(string $domain, int $dkimKeyLength = 2048): array
    {
        $data = [
            'domain' => $domain,
            'dkim_key_length' => $dkimKeyLength,
        ];

        return $this->client->post('/api/domains', $data);
    }

    /**
     * Get domain details
     *
     * @param string $domain Domain name
     * @return array{success: bool, data: Domain}
     * @throws ApiException
     */
    public function getDomain(string $domain): array
    {
        $response = $this->client->get("/api/domains/{$domain}");

        return [
            'success' => true,
            'data' => new Domain($response['data']),
        ];
    }

    /**
     * Update domain configuration
     *
     * @param string $domain Domain name
     * @param array $config Configuration (open_track, click_track, unsub_track)
     * @return array{success: bool, data: array}
     * @throws ApiException
     */
    public function updateDomain(string $domain, array $config): array
    {
        $response = $this->client->put("/api/domains/{$domain}", $config);

        return [
            'success' => true,
            'data' => $response['data'],
        ];
    }

    /**
     * Delete a domain
     *
     * @param string $domain Domain name
     * @return array{success: bool, message: string}
     * @throws ApiException
     */
    public function deleteDomain(string $domain): array
    {
        return $this->client->delete("/api/domains/{$domain}");
    }

    /**
     * Setup authentication domain
     *
     * @param string $domain Domain name
     * @param array $config Configuration (auth_domain, dkim_key_length)
     * @return array{success: bool, data: array}
     * @throws ApiException
     */
    public function setupAuthDomain(string $domain, array $config): array
    {
        return $this->client->post("/api/domains/{$domain}/setup-auth-domain", $config);
    }

    /**
     * Verify mandatory DNS records
     *
     * @param string $domain Domain name
     * @return array{success: bool, records: array}
     * @throws ApiException
     */
    public function verifyMandatoryRecords(string $domain): array
    {
        return $this->client->post("/api/domains/{$domain}/verify-mandatory");
    }

    /**
     * Verify authentication domain DNS records
     *
     * @param string $domain Domain name
     * @return array{success: bool, records: array}
     * @throws ApiException
     */
    public function verifyAuthDomainRecords(string $domain): array
    {
        return $this->client->post("/api/domains/{$domain}/verify-auth-domain");
    }
}