<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Api;

use KirimEmail\Smtp\Client\SmtpClient;
use KirimEmail\Smtp\Model\Credential;
use KirimEmail\Smtp\Model\Pagination;
use KirimEmail\Smtp\Exception\ApiException;

class CredentialsApi
{
    private SmtpClient $client;

    public function __construct(SmtpClient $client)
    {
        $this->client = $client;
    }

    /**
     * List domain credentials
     *
     * @param string $domain Domain name
     * @param array $params Query parameters (limit, page)
     * @return array{data: Credential[], domain: string, pagination: Pagination}
     * @throws ApiException
     */
    public function listCredentials(string $domain, array $params = []): array
    {
        $response = $this->client->get("/api/domains/{$domain}/credentials", $params);

        $credentials = [];
        // Handle nested data structure for list response
        $credentialList = $response['data']['data'] ?? $response['data'] ?? [];
        foreach ($credentialList as $credentialData) {
            $credentials[] = new Credential($credentialData);
        }

        $pagination = isset($response['data']['pagination']) ? new Pagination($response['data']['pagination']) : null;

        return [
            'data' => $credentials,
            'domain' => $response['domain'] ?? $domain,
            'pagination' => $pagination,
        ];
    }

    /**
     * Create a new credential
     *
     * @param string $domain Domain name
     * @param string $username SMTP username
     * @return array{success: bool, message: string, data: array{credential: Credential, password: string, remote_synced: bool}}
     * @throws ApiException
     */
    public function createCredential(string $domain, string $username): array
    {
        $data = ['username' => $username];
        $response = $this->client->post("/api/domains/{$domain}/credentials", $data);

        // Transform credential data to model and flatten password fields
        if (isset($response['data']['credential'])) {
            $credentialData = $response['data']['credential'];
            $credential = new Credential($credentialData);

            // Add password and remote_synced to the credential model
            if (isset($response['data']['password'])) {
                $credential->setPassword($response['data']['password']);
            }
            if (isset($response['data']['remote_synced'])) {
                $credential->setRemoteSynced($response['data']['remote_synced']);
            }

            $response['data']['credential'] = $credential;
        }

        return $response;
    }

    /**
     * Get credential details
     *
     * @param string $domain Domain name
     * @param string $credential Credential ID
     * @return array{success: bool, data: Credential}
     * @throws ApiException
     */
    public function getCredential(string $domain, string $credential): array
    {
        $response = $this->client->get("/api/domains/{$domain}/credentials/{$credential}");

        return [
            'success' => true,
            'data' => new Credential($response['data']),
        ];
    }

    /**
     * Delete a credential
     *
     * @param string $domain Domain name
     * @param string $credential Credential ID
     * @return array{success: bool, message: string}
     * @throws ApiException
     */
    public function deleteCredential(string $domain, string $credential): array
    {
        return $this->client->delete("/api/domains/{$domain}/credentials/{$credential}");
    }

    /**
     * Reset credential password
     *
     * @param string $domain Domain name
     * @param string $credential Credential ID
     * @return array{success: bool, message: string, data: array{credential: Credential, new_password: string, strength_info: array, remote_synced: bool}}
     * @throws ApiException
     */
    public function resetPassword(string $domain, string $credential): array
    {
        $response = $this->client->put("/api/domains/{$domain}/credentials/{$credential}/reset-password");

        // Transform credential data to model and flatten password fields
        if (isset($response['data']['credential'])) {
            $credentialData = $response['data']['credential'];
            $credentialModel = new Credential($credentialData);

            // Add new_password to password property for consistency, plus other fields
            if (isset($response['data']['new_password'])) {
                $credentialModel->setPassword($response['data']['new_password']);
            }
            if (isset($response['data']['strength_info'])) {
                $credentialModel->setStrengthInfo($response['data']['strength_info']);
            }
            if (isset($response['data']['remote_synced'])) {
                $credentialModel->setRemoteSynced($response['data']['remote_synced']);
            }

            $response['data']['credential'] = $credentialModel;
        }

        return $response;
    }
}