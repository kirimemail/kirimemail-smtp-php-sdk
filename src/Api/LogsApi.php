<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Api;

use KirimEmail\Smtp\Client\SmtpClient;
use KirimEmail\Smtp\Model\LogEntry;
use KirimEmail\Smtp\Model\Pagination;
use KirimEmail\Smtp\Exception\ApiException;

class LogsApi
{
    private SmtpClient $client;

    public function __construct(SmtpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get domain logs
     *
     * @param string $domain Domain name
     * @param array $params Query parameters (start, end, sender, recipient, limit, offset)
     * @return array{data: LogEntry[], count: int, offset: int, limit: int, pagination: Pagination}
     * @throws ApiException
     */
    public function getLogs(string $domain, array $params = []): array
    {
        $this->validateLogParams($params);
        $response = $this->client->get("/api/domains/{$domain}/log", $params);

        $logs = [];
        foreach ($response['data'] ?? [] as $logData) {
            $logs[] = new LogEntry($logData);
        }

        $pagination = isset($response['pagination']) ? new Pagination($response['pagination']) : null;

        return [
            'data' => $logs,
            'count' => $response['count'] ?? count($logs),
            'offset' => $response['offset'] ?? 0,
            'limit' => $response['limit'] ?? 1000,
            'pagination' => $pagination,
        ];
    }

    /**
     * Get logs for a specific message
     *
     * @param string $domain Domain name
     * @param string $messageGuid Message GUID
     * @return array{data: LogEntry[]}
     * @throws ApiException
     */
    public function getMessageLogs(string $domain, string $messageGuid): array
    {
        $response = $this->client->get("/api/domains/{$domain}/log/{$messageGuid}");

        $logs = [];
        foreach ($response['data'] ?? [] as $logData) {
            $logs[] = new LogEntry($logData);
        }

        return [
            'data' => $logs,
        ];
    }

    /**
     * Stream logs in real-time (Server-Sent Events)
     *
     * @param string $domain Domain name
     * @param array $params Query parameters (start, end, sender, recipient, limit)
     * @return \Generator<LogEntry>
     * @throws ApiException
     */
    public function streamLogs(string $domain, array $params = []): \Generator
    {
        $this->validateLogParams($params);

        // Set default limit for streaming
        if (!isset($params['limit'])) {
            $params['limit'] = 50000;
        }

        foreach ($this->client->stream("/api/domains/{$domain}/log/stream", $params) as $event) {
            if (isset($event['data'])) {
                yield new LogEntry($event['data']);
            }
        }
    }

    /**
     * Get logs with date range filtering
     *
     * @param string $domain Domain name
     * @param \DateTimeInterface $startDate Start date
     * @param \DateTimeInterface $endDate End date
     * @param array $additionalParams Additional parameters
     * @return array{data: LogEntry[], count: int, offset: int, limit: int, pagination: Pagination}
     * @throws ApiException
     */
    public function getLogsByDateRange(string $domain, \DateTimeInterface $startDate, \DateTimeInterface $endDate, array $additionalParams = []): array
    {
        $params = array_merge($additionalParams, [
            'start' => $startDate->format('c'),
            'end' => $endDate->format('c'),
        ]);

        return $this->getLogs($domain, $params);
    }

    /**
     * Get logs filtered by sender
     *
     * @param string $domain Domain name
     * @param string $sender Sender email address
     * @param array $additionalParams Additional parameters
     * @return array{data: LogEntry[], count: int, offset: int, limit: int, pagination: Pagination}
     * @throws ApiException
     */
    public function getLogsBySender(string $domain, string $sender, array $additionalParams = []): array
    {
        $params = array_merge($additionalParams, [
            'sender' => $sender,
        ]);

        return $this->getLogs($domain, $params);
    }

    /**
     * Get logs filtered by recipient
     *
     * @param string $domain Domain name
     * @param string $recipient Recipient email address
     * @param array $additionalParams Additional parameters
     * @return array{data: LogEntry[], count: int, offset: int, limit: int, pagination: Pagination}
     * @throws ApiException
     */
    public function getLogsByRecipient(string $domain, string $recipient, array $additionalParams = []): array
    {
        $params = array_merge($additionalParams, [
            'recipient' => $recipient,
        ]);

        return $this->getLogs($domain, $params);
    }

    
    /**
     * Validate log parameters
     *
     * @param array $params
     * @throws ApiException
     */
    private function validateLogParams(array $params): void
    {
        // Validate date formats
        if (isset($params['start'])) {
            if (!$this->isValidDateTime($params['start'])) {
                throw new ApiException("Invalid start date format. Use ISO8601 format.");
            }
        }

        if (isset($params['end'])) {
            if (!$this->isValidDateTime($params['end'])) {
                throw new ApiException("Invalid end date format. Use ISO8601 format.");
            }
        }

        // Validate email addresses
        if (isset($params['sender']) && !filter_var($params['sender'], FILTER_VALIDATE_EMAIL)) {
            throw new ApiException("Invalid sender email address.");
        }

        if (isset($params['recipient']) && !filter_var($params['recipient'], FILTER_VALIDATE_EMAIL)) {
            throw new ApiException("Invalid recipient email address.");
        }

        // Validate limit
        if (isset($params['limit'])) {
            $limit = (int) $params['limit'];
            if ($limit < 1 || $limit > 10000) {
                throw new ApiException("Limit must be between 1 and 10000.");
            }
        }

        // Validate offset
        if (isset($params['offset'])) {
            $offset = (int) $params['offset'];
            if ($offset < 0) {
                throw new ApiException("Offset must be greater than or equal to 0.");
            }
        }
    }

    /**
     * Validate ISO8601 datetime format
     *
     * @param string $dateTime
     * @return bool
     */
    private function isValidDateTime(string $dateTime): bool
    {
        $format = 'Y-m-d\TH:i:sP';
        $date = \DateTime::createFromFormat($format, $dateTime);
        return $date && $date->format($format) === $dateTime;
    }
}