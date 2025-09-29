<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Tests\Api;

use PHPUnit\Framework\TestCase;
use KirimEmail\Smtp\Client\SmtpClient;
use KirimEmail\Smtp\Api\LogsApi;
use KirimEmail\Smtp\Model\LogEntry;
use KirimEmail\Smtp\Model\Pagination;
use KirimEmail\Smtp\Exception\ApiException;

class LogsApiTest extends TestCase
{
    private SmtpClient $mockClient;
    private LogsApi $logsApi;

    protected function setUp(): void
    {
        $this->mockClient = $this->createMock(SmtpClient::class);
        $this->logsApi = new LogsApi($this->mockClient);
    }

    public function testGetLogs()
    {
        $mockResponse = [
            'data' => [
                [
                    'id' => 1,
                    'user_guid' => 'user-guid-123',
                    'user_domain_guid' => 'domain-guid-456',
                    'event_type' => 'delivered',
                    'message_guid' => 'msg-guid-789',
                    'timestamp' => 1640995200
                ]
            ],
            'count' => 1,
            'offset' => 0,
            'limit' => 1000,
            'pagination' => [
                'total' => 1,
                'page' => 1,
                'limit' => 1000
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/log', ['limit' => 10])
            ->willReturn($mockResponse);

        $result = $this->logsApi->getLogs('example.com', ['limit' => 10]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('count', $result);
        $this->assertArrayHasKey('offset', $result);
        $this->assertArrayHasKey('limit', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertCount(1, $result['data']);
        $this->assertInstanceOf(LogEntry::class, $result['data'][0]);
        $this->assertEquals('delivered', $result['data'][0]->getEventType());
        $this->assertEquals('msg-guid-789', $result['data'][0]->getMessageGuid());
    }

    public function testGetMessageLogs()
    {
        $mockResponse = [
            'data' => [
                [
                    'id' => 2,
                    'user_guid' => 'user-guid-234',
                    'user_domain_guid' => 'domain-guid-567',
                    'event_type' => 'opened',
                    'message_guid' => 'msg-guid-890',
                    'timestamp' => 1640995300
                ],
                [
                    'id' => 3,
                    'user_guid' => 'user-guid-345',
                    'user_domain_guid' => 'domain-guid-678',
                    'event_type' => 'clicked',
                    'message_guid' => 'msg-guid-890',
                    'timestamp' => 1640995400
                ]
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/log/msg-guid-890')
            ->willReturn($mockResponse);

        $result = $this->logsApi->getMessageLogs('example.com', 'msg-guid-890');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertCount(2, $result['data']);
        $this->assertEquals('opened', $result['data'][0]->getEventType());
        $this->assertEquals('clicked', $result['data'][1]->getEventType());
    }

    public function testGetLogsByDateRange()
    {
        $startDate = new \DateTime('2022-01-01');
        $endDate = new \DateTime('2022-01-31');

        $mockResponse = [
            'data' => [
                [
                    'id' => 4,
                    'event_type' => 'bounced',
                    'message_guid' => 'msg-guid-111',
                    'timestamp' => 1640995500
                ]
            ],
            'count' => 1,
            'offset' => 0,
            'limit' => 1000
        ];

        $expectedParams = [
            'start' => $startDate->format('c'),
            'end' => $endDate->format('c')
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/log', $expectedParams)
            ->willReturn($mockResponse);

        $result = $this->logsApi->getLogsByDateRange('example.com', $startDate, $endDate);

        $this->assertIsArray($result);
        $this->assertCount(1, $result['data']);
        $this->assertEquals('bounced', $result['data'][0]->getEventType());
    }

    public function testGetLogsBySender()
    {
        $mockResponse = [
            'data' => [
                [
                    'id' => 5,
                    'event_type' => 'delivered',
                    'message_guid' => 'msg-guid-222',
                    'timestamp' => 1640995600
                ]
            ],
            'count' => 1
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/log', ['sender' => 'sender@example.com'])
            ->willReturn($mockResponse);

        $result = $this->logsApi->getLogsBySender('example.com', 'sender@example.com');

        $this->assertIsArray($result);
        $this->assertCount(1, $result['data']);
    }

    public function testGetLogsByRecipient()
    {
        $mockResponse = [
            'data' => [
                [
                    'id' => 6,
                    'event_type' => 'delivered',
                    'message_guid' => 'msg-guid-333',
                    'timestamp' => 1640995700
                ]
            ],
            'count' => 1
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/log', ['recipient' => 'recipient@example.com'])
            ->willReturn($mockResponse);

        $result = $this->logsApi->getLogsByRecipient('example.com', 'recipient@example.com');

        $this->assertIsArray($result);
        $this->assertCount(1, $result['data']);
    }

    
    public function testStreamLogs()
    {
        $mockEvents = [
            ['data' => ['event_type' => 'delivered', 'message_guid' => 'msg-1', 'timestamp' => 1640995200]],
            ['data' => ['event_type' => 'opened', 'message_guid' => 'msg-2', 'timestamp' => 1640995300]],
            ['data' => ['event_type' => 'clicked', 'message_guid' => 'msg-3', 'timestamp' => 1640995400]]
        ];

        $this->mockClient->expects($this->once())
            ->method('stream')
            ->with('/api/domains/example.com/log/stream', ['limit' => 1000])
            ->willReturn($this->yieldEvents($mockEvents));

        $events = [];
        foreach ($this->logsApi->streamLogs('example.com', ['limit' => 1000]) as $log) {
            $events[] = $log;
        }

        $this->assertCount(3, $events);
        $this->assertEquals('delivered', $events[0]->getEventType());
        $this->assertEquals('opened', $events[1]->getEventType());
        $this->assertEquals('clicked', $events[2]->getEventType());
    }

    public function testGetLogsWithEmptyResponse()
    {
        $mockResponse = [
            'data' => [],
            'count' => 0,
            'offset' => 0,
            'limit' => 1000,
            'pagination' => [
                'total' => 0,
                'page' => 1,
                'limit' => 1000
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/log')
            ->willReturn($mockResponse);

        $result = $this->logsApi->getLogs('example.com');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertEmpty($result['data']);
        $this->assertEquals(0, $result['count']);
    }

    public function testGetLogsWithDateTimeValidation()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Invalid start date format. Use ISO8601 format.');

        // The validation happens in the API class before calling the client
        $this->mockClient->expects($this->never())
            ->method('get');

        $this->logsApi->getLogs('example.com', ['start' => 'invalid-date']);
    }

    public function testGetLogsWithEmailValidation()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Invalid sender email address.');

        // The validation happens in the API class before calling the client
        $this->mockClient->expects($this->never())
            ->method('get');

        $this->logsApi->getLogs('example.com', ['sender' => 'invalid-email']);
    }

    public function testGetLogsWithLimitValidation()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Limit must be between 1 and 10000.');

        // The validation happens in the API class before calling the client
        $this->mockClient->expects($this->never())
            ->method('get');

        $this->logsApi->getLogs('example.com', ['limit' => 15000]);
    }

    public function testGetLogsWithOffsetValidation()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Offset must be greater than or equal to 0.');

        // The validation happens in the API class before calling the client
        $this->mockClient->expects($this->never())
            ->method('get');

        $this->logsApi->getLogs('example.com', ['offset' => -1]);
    }

    public function testGetLogsThrowsException()
    {
        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/log')
            ->willThrowException(new ApiException('API Error'));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('API Error');

        $this->logsApi->getLogs('example.com');
    }

    
    public function testStreamLogsWithDefaultLimit()
    {
        $mockEvents = [
            ['data' => ['event_type' => 'delivered', 'message_guid' => 'msg-1', 'timestamp' => 1640995200]]
        ];

        $this->mockClient->expects($this->once())
            ->method('stream')
            ->with('/api/domains/example.com/log/stream', ['limit' => 50000]) // Default limit
            ->willReturn($this->yieldEvents($mockEvents));

        $events = [];
        foreach ($this->logsApi->streamLogs('example.com') as $log) {
            $events[] = $log;
        }

        $this->assertCount(1, $events);
    }

    /**
     * Helper method to simulate generator yielding events
     */
    private function yieldEvents(array $events)
    {
        foreach ($events as $event) {
            yield $event;
        }
    }
}