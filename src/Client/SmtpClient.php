<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\MultipartStream;
use Psr\Http\Message\ResponseInterface;
use KirimEmail\Smtp\Exception\ApiException;
use KirimEmail\Smtp\Exception\AuthenticationException;
use KirimEmail\Smtp\Exception\ValidationException;
use KirimEmail\Smtp\Exception\NotFoundException;
use KirimEmail\Smtp\Exception\ServerException;

class SmtpClient
{
    private GuzzleClient $httpClient;
    private ?string $username;
    private ?string $token;
    private ?string $domainApiKey;
    private ?string $domainApiSecret;
    private string $baseUrl;

    public function __construct(
        ?string $username = null,
        ?string $token = null,
        ?string $domainApiKey = null,
        ?string $domainApiSecret = null,
        string $baseUrl = 'https://smtp-app.kirim.email'
    ) {
        $this->username = $username;
        $this->token = $token;
        $this->domainApiKey = $domainApiKey;
        $this->domainApiSecret = $domainApiSecret;
        $this->baseUrl = rtrim($baseUrl, '/');

        $this->httpClient = new GuzzleClient([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'connect_timeout' => 10,
            'http_errors' => false,
            'headers' => [
                'User-Agent' => 'KirimEmail-PHP-SDK/1.0.0',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function get(string $endpoint, array $params = [], array $headers = []): array
    {
        return $this->request('GET', $endpoint, [
            'query' => $params,
            'headers' => $headers,
        ]);
    }

    public function post(string $endpoint, array $data = [], array $headers = []): array
    {
        return $this->request('POST', $endpoint, [
            'json' => $data,
            'headers' => $headers,
        ]);
    }

    public function postMultipart(string $endpoint, array $data = [], array $files = [], array $headers = []): array
    {
        $multipart = [];

        // Add form fields
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $multipart[] = [
                        'name' => $key . '[]',
                        'contents' => $item,
                    ];
                }
            } else {
                $multipart[] = [
                    'name' => $key,
                    'contents' => $value,
                ];
            }
        }

        // Add files
        foreach ($files as $key => $file) {
            if (is_array($file)) {
                foreach ($file as $index => $filePath) {
                    $multipart[] = [
                        'name' => $key . '[' . $index . ']',
                        'contents' => fopen($filePath, 'r'),
                        'filename' => basename($filePath),
                    ];
                }
            } else {
                $multipart[] = [
                    'name' => $key,
                    'contents' => fopen($file, 'r'),
                    'filename' => basename($file),
                ];
            }
        }

        return $this->request('POST', $endpoint, [
            'multipart' => $multipart,
            'headers' => $headers,
        ]);
    }

    public function put(string $endpoint, array $data = [], array $headers = []): array
    {
        return $this->request('PUT', $endpoint, [
            'json' => $data,
            'headers' => $headers,
        ]);
    }

    public function delete(string $endpoint, array $params = [], array $headers = []): array
    {
        return $this->request('DELETE', $endpoint, [
            'query' => $params,
            'headers' => $headers,
        ]);
    }

    public function request(string $method, string $endpoint, array $options = []): array
    {
        $options = $this->addAuthenticationHeaders($options, $endpoint);

        try {
            $response = $this->httpClient->request($method, $endpoint, $options);
            return $this->parseResponse($response);
        } catch (GuzzleRequestException $e) {
            if ($e->hasResponse()) {
                $this->parseResponse($e->getResponse());
            }
            throw new ApiException('Network error: ' . $e->getMessage(), 0, $e);
        }
    }

    private function addAuthenticationHeaders(array $options, string $endpoint): array
    {
        $headers = $options['headers'] ?? [];

        // Determine authentication type based on endpoint
        if (strpos($endpoint, '/api/v4/') === 0) {
            // Domain API Key authentication
            if ($this->domainApiKey && $this->domainApiSecret) {
                $authHeader = 'Basic ' . base64_encode($this->domainApiKey . ':' . $this->domainApiSecret);
                $headers['Authorization'] = $authHeader;

                // Add domain header if not already present
                if (!isset($headers['domain']) && !isset($options['multipart'])) {
                    $headers['domain'] = $this->extractDomainFromEndpoint($endpoint);
                }
            }
        } else {
            // Basic authentication
            if ($this->username && $this->token) {
                $authHeader = 'Basic ' . base64_encode($this->username . ':' . $this->token);
                $headers['Authorization'] = $authHeader;
            }
        }

        $options['headers'] = $headers;
        return $options;
    }

    private function extractDomainFromEndpoint(string $endpoint): ?string
    {
        // Extract domain from endpoint path if possible
        if (preg_match('/\/api\/domains\/([^\/]+)/', $endpoint, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function parseResponse(ResponseInterface $response): array
    {
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();

        if (empty($body)) {
            return ['success' => $statusCode < 400];
        }

        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException('Invalid JSON response: ' . json_last_error_msg());
        }

        // Handle error responses
        if ($statusCode >= 400) {
            $this->handleErrorResponse($statusCode, $data);
        }

        return $data;
    }

    private function handleErrorResponse(int $statusCode, array $data): void
    {
        $message = $data['message'] ?? $data['error'] ?? 'Unknown API error';

        switch ($statusCode) {
            case 400:
                throw new ValidationException($message, $data['errors'] ?? []);
            case 401:
                throw new AuthenticationException($message);
            case 403:
                throw new AuthenticationException($message);
            case 404:
                throw new NotFoundException($message);
            case 422:
                throw new ValidationException($message, $data['errors'] ?? []);
            default:
                if ($statusCode >= 500) {
                    throw new ServerException($message);
                }
                throw new ApiException($message, $statusCode);
        }
    }

    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->httpClient = new GuzzleClient([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
            'connect_timeout' => 10,
            'http_errors' => false,
            'headers' => [
                'User-Agent' => 'KirimEmail-PHP-SDK/1.0.0',
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function stream(string $endpoint, array $params = [], array $headers = []): \Generator
    {
        $options = [
            'query' => $params,
            'headers' => $headers,
            'stream' => true,
        ];

        $options = $this->addAuthenticationHeaders($options, $endpoint);

        $response = $this->httpClient->get($endpoint, $options);
        $stream = $response->getBody();

        while (!$stream->eof()) {
            $line = $stream->readline();
            if (!empty($line) && $line !== "\n") {
                // Remove "data: " prefix from SSE format
                if (strpos($line, 'data: ') === 0) {
                    $data = substr($line, 6);
                    if ($data !== '[DONE]') {
                        $decoded = json_decode($data, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            yield $decoded;
                        }
                    }
                }
            }
        }
    }
}