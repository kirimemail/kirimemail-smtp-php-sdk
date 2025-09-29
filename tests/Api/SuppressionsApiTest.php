<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Tests\Api;

use PHPUnit\Framework\TestCase;
use KirimEmail\Smtp\Client\SmtpClient;
use KirimEmail\Smtp\Api\SuppressionsApi;
use KirimEmail\Smtp\Model\Suppression;
use KirimEmail\Smtp\Model\Pagination;
use KirimEmail\Smtp\Exception\ApiException;

class SuppressionsApiTest extends TestCase
{
    private SmtpClient $mockClient;
    private SuppressionsApi $suppressionsApi;

    protected function setUp(): void
    {
        $this->mockClient = $this->createMock(SmtpClient::class);
        $this->suppressionsApi = new SuppressionsApi($this->mockClient);
    }

    public function testGetSuppressions()
    {
        $mockResponse = [
            'data' => [
                [
                    'id' => 1,
                    'type' => 'bounce',
                    'recipient_type' => 'email',
                    'recipient' => 'bounced@example.com',
                    'description' => 'Hard bounce',
                    'source' => 'auto',
                    'created_at' => 1640995200,
                    'modified_at' => 1640995200
                ]
            ],
            'pagination' => [
                'total' => 1,
                'page' => 1,
                'limit' => 10,
                'offset' => 0
            ],
            'filters' => ['type' => 'bounce']
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/suppressions', ['type' => 'bounce', 'page' => 1])
            ->willReturn($mockResponse);

        $result = $this->suppressionsApi->getSuppressions('example.com', ['type' => 'bounce', 'page' => 1]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertArrayHasKey('filters', $result);
        $this->assertCount(1, $result['data']);
        $this->assertInstanceOf(Suppression::class, $result['data'][0]);
        $this->assertEquals('bounce', $result['data'][0]->getType());
        $this->assertEquals('bounced@example.com', $result['data'][0]->getRecipient());
    }

    public function testGetUnsubscribeSuppressions()
    {
        $mockResponse = [
            'data' => [
                [
                    'id' => 2,
                    'type' => 'unsubscribe',
                    'recipient_type' => 'email',
                    'recipient' => 'unsub@example.com',
                    'description' => 'User unsubscribed',
                    'source' => 'manual',
                    'created_at' => 1640995300
                ]
            ],
            'pagination' => [
                'total' => 1,
                'page' => 1,
                'limit' => 10
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/suppressions', ['type' => 'unsubscribe'])
            ->willReturn($mockResponse);

        $result = $this->suppressionsApi->getUnsubscribeSuppressions('example.com');

        $this->assertIsArray($result);
        $this->assertCount(1, $result['data']);
        $this->assertEquals('unsubscribe', $result['data'][0]->getType());
        $this->assertTrue($result['data'][0]->isUnsubscribe());
    }

    public function testGetBounceSuppressions()
    {
        $mockResponse = [
            'data' => [
                [
                    'id' => 3,
                    'type' => 'bounce',
                    'recipient_type' => 'email',
                    'recipient' => 'bounced2@example.com',
                    'description' => 'Mailbox full',
                    'source' => 'auto',
                    'created_at' => 1640995400
                ]
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/suppressions', ['type' => 'bounce'])
            ->willReturn($mockResponse);

        $result = $this->suppressionsApi->getBounceSuppressions('example.com');

        $this->assertIsArray($result);
        $this->assertCount(1, $result['data']);
        $this->assertEquals('bounce', $result['data'][0]->getType());
        $this->assertTrue($result['data'][0]->isBounce());
    }

    public function testGetWhitelistSuppressions()
    {
        $mockResponse = [
            'data' => [
                [
                    'id' => 4,
                    'type' => 'whitelist',
                    'recipient_type' => 'email',
                    'recipient' => 'whitelisted@example.com',
                    'description' => 'Manually whitelisted',
                    'source' => 'manual',
                    'created_at' => 1640995500
                ]
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/suppressions', ['type' => 'whitelist'])
            ->willReturn($mockResponse);

        $result = $this->suppressionsApi->getWhitelistSuppressions('example.com');

        $this->assertIsArray($result);
        $this->assertCount(1, $result['data']);
        $this->assertEquals('whitelist', $result['data'][0]->getType());
    }

    public function testGetSuppressionsByType()
    {
        $mockResponse = [
            'data' => [
                [
                    'id' => 5,
                    'type' => 'unsubscribe',
                    'recipient' => 'test@example.com',
                    'created_at' => 1640995600
                ]
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/suppressions', ['type' => 'unsubscribe', 'limit' => 5])
            ->willReturn($mockResponse);

        $result = $this->suppressionsApi->getSuppressionsByType('example.com', 'unsubscribe', ['limit' => 5]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result['data']);
        $this->assertEquals('unsubscribe', $result['data'][0]->getType());
    }

    public function testSearchSuppressions()
    {
        $mockResponse = [
            'data' => [
                [
                    'id' => 6,
                    'type' => 'bounce',
                    'recipient' => 'search@example.com',
                    'created_at' => 1640995700
                ]
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/suppressions', ['search' => 'search@example.com', 'page' => 1])
            ->willReturn($mockResponse);

        $result = $this->suppressionsApi->searchSuppressions('example.com', 'search@example.com', ['page' => 1]);

        $this->assertIsArray($result);
        $this->assertCount(1, $result['data']);
        $this->assertEquals('search@example.com', $result['data'][0]->getRecipient());
    }

    public function testGetSuppressionsPaginated()
    {
        $mockResponse = [
            'data' => [
                [
                    'id' => 7,
                    'type' => 'unsubscribe',
                    'recipient' => 'page2@example.com',
                    'created_at' => 1640995800
                ]
            ],
            'pagination' => [
                'total' => 25,
                'page' => 2,
                'limit' => 10,
                'offset' => 10
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/suppressions', ['page' => 2, 'per_page' => 10])
            ->willReturn($mockResponse);

        $result = $this->suppressionsApi->getSuppressionsPaginated('example.com', 2, 10);

        $this->assertIsArray($result);
        $this->assertCount(1, $result['data']);
        $this->assertEquals(2, $result['pagination']->getPage());
        $this->assertEquals(10, $result['pagination']->getLimit());
    }

    
    public function testGetSuppressionsCreatedAfter()
    {
        $startDate = new \DateTime('2022-01-01');
        $mockResponse = [
            'data' => [
                [
                    'id' => 9,
                    'type' => 'unsubscribe',
                    'recipient' => 'recent@example.com',
                    'created_at' => 1640996000 // After start date
                ]
            ],
            'pagination' => ['total' => 1]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/suppressions')
            ->willReturn($mockResponse);

        $result = $this->suppressionsApi->getSuppressionsCreatedAfter('example.com', $startDate);

        $this->assertIsArray($result);
        $this->assertCount(1, $result['data']);
        $this->assertEquals('recent@example.com', $result['data'][0]->getRecipient());
    }

    public function testGetSuppressionsWithEmptyResponse()
    {
        $mockResponse = [
            'data' => [],
            'pagination' => [
                'total' => 0,
                'page' => 1,
                'limit' => 10,
                'offset' => 0
            ],
            'filters' => []
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/suppressions')
            ->willReturn($mockResponse);

        $result = $this->suppressionsApi->getSuppressions('example.com');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertArrayHasKey('filters', $result);
        $this->assertEmpty($result['data']);
        $this->assertEquals(0, $result['pagination']->getTotal());
    }

    public function testGetSuppressionsWithInvalidType()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Invalid suppression type. Must be one of: unsubscribe, bounce, whitelist');

        // The validation happens in the API class before calling the client
        $this->mockClient->expects($this->never())
            ->method('get');

        $this->suppressionsApi->getSuppressions('example.com', ['type' => 'invalid']);
    }

    public function testGetSuppressionsWithInvalidPage()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Page must be greater than or equal to 1.');

        // The validation happens in the API class before calling the client
        $this->mockClient->expects($this->never())
            ->method('get');

        $this->suppressionsApi->getSuppressions('example.com', ['page' => 0]);
    }

    public function testGetSuppressionsWithInvalidPerPage()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Per page must be between 10 and 100.');

        // The validation happens in the API class before calling the client
        $this->mockClient->expects($this->never())
            ->method('get');

        $this->suppressionsApi->getSuppressions('example.com', ['per_page' => 5]);
    }

    public function testGetSuppressionsThrowsException()
    {
        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/suppressions')
            ->willThrowException(new ApiException('API Error'));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('API Error');

        $this->suppressionsApi->getSuppressions('example.com');
    }

  }