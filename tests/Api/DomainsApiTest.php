<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Tests\Api;

use PHPUnit\Framework\TestCase;
use KirimEmail\Smtp\Client\SmtpClient;
use KirimEmail\Smtp\Api\DomainsApi;
use KirimEmail\Smtp\Model\Domain;
use KirimEmail\Smtp\Model\Pagination;
use KirimEmail\Smtp\Exception\ApiException;

class DomainsApiTest extends TestCase
{
    private SmtpClient $mockClient;
    private DomainsApi $domainsApi;

    protected function setUp(): void
    {
        $this->mockClient = $this->createMock(SmtpClient::class);
        $this->domainsApi = new DomainsApi($this->mockClient);
    }

    public function testListDomains()
    {
        $mockResponse = [
            'data' => [
                [
                    'id' => 1,
                    'domain' => 'example.com',
                    'is_verified' => true,
                    'created_at' => 1640995200,
                    'modified_at' => 1640995200
                ]
            ],
            'pagination' => [
                'total' => 1,
                'page' => 1,
                'limit' => 10,
                'offset' => 0
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains', ['limit' => 10])
            ->willReturn($mockResponse);

        $result = $this->domainsApi->listDomains(['limit' => 10]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertCount(1, $result['data']);
        $this->assertInstanceOf(Domain::class, $result['data'][0]);
        $this->assertInstanceOf(Pagination::class, $result['pagination']);
        $this->assertEquals('example.com', $result['data'][0]->getDomain());
    }

    public function testCreateDomain()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Domain created successfully',
            'data' => [
                'id' => 2,
                'domain' => 'newdomain.com',
                'is_verified' => false,
                'created_at' => 1640995200,
                'modified_at' => 1640995200
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/api/domains', ['domain' => 'newdomain.com', 'dkim_key_length' => 2048])
            ->willReturn($mockResponse);

        $result = $this->domainsApi->createDomain('newdomain.com', 2048);

        $this->assertTrue($result['success']);
        $this->assertEquals('Domain created successfully', $result['message']);
        $this->assertEquals('newdomain.com', $result['data']['domain']);
    }

    public function testGetDomain()
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                'id' => 3,
                'domain' => 'test.com',
                'is_verified' => true,
                'auth_domain' => 'mail.test.com',
                'created_at' => 1640995200,
                'modified_at' => 1640995200
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/test.com')
            ->willReturn($mockResponse);

        $result = $this->domainsApi->getDomain('test.com');

        $this->assertTrue($result['success']);
        $this->assertInstanceOf(Domain::class, $result['data']);
        $this->assertEquals('test.com', $result['data']->getDomain());
        $this->assertEquals('mail.test.com', $result['data']->getAuthDomain());
    }

    public function testUpdateDomain()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Domain updated successfully',
            'data' => [
                'id' => 4,
                'domain' => 'updated.com',
                'click_track' => true,
                'open_track' => true,
                'unsub_track' => false,
                'modified_at' => 1640995300
            ]
        ];

        $updateData = [
            'click_track' => true,
            'open_track' => true,
            'unsub_track' => false
        ];

        $this->mockClient->expects($this->once())
            ->method('put')
            ->with('/api/domains/updated.com', $updateData)
            ->willReturn($mockResponse);

        $result = $this->domainsApi->updateDomain('updated.com', $updateData);

        $this->assertTrue($result['success']);
        $this->assertEquals('updated.com', $result['data']['domain']);
    }

    public function testDeleteDomain()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Domain deleted successfully'
        ];

        $this->mockClient->expects($this->once())
            ->method('delete')
            ->with('/api/domains/delete.com')
            ->willReturn($mockResponse);

        $result = $this->domainsApi->deleteDomain('delete.com');

        $this->assertTrue($result['success']);
        $this->assertEquals('Domain deleted successfully', $result['message']);
    }

    public function testSetupAuthDomain()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Auth domain setup initiated',
            'data' => [
                'auth_domain' => 'auth.example.com',
                'dns_selector' => 'trx_ke',
                'dns_record' => 'v=DKIM1; k=rsa; p=...'
            ]
        ];

        $config = [
            'auth_domain' => 'auth.example.com',
            'dkim_key_length' => 2048
        ];

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/api/domains/example.com/setup-auth-domain', $config)
            ->willReturn($mockResponse);

        $result = $this->domainsApi->setupAuthDomain('example.com', $config);

        $this->assertTrue($result['success']);
        $this->assertEquals('auth.example.com', $result['data']['auth_domain']);
    }

    public function testVerifyMandatoryRecords()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'DNS verification completed',
            'data' => [
                'verified' => true,
                'records' => [
                    'mx' => true,
                    'spf' => true,
                    'dkim' => true
                ]
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/api/domains/example.com/verify-mandatory')
            ->willReturn($mockResponse);

        $result = $this->domainsApi->verifyMandatoryRecords('example.com');

        $this->assertTrue($result['success']);
        $this->assertTrue($result['data']['verified']);
    }

    public function testVerifyAuthDomainRecords()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Auth domain verification completed',
            'data' => [
                'verified' => true,
                'auth_domain' => 'auth.example.com'
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/api/domains/example.com/verify-auth-domain')
            ->willReturn($mockResponse);

        $result = $this->domainsApi->verifyAuthDomainRecords('example.com');

        $this->assertTrue($result['success']);
        $this->assertTrue($result['data']['verified']);
    }

    public function testListDomainsWithEmptyResponse()
    {
        $mockResponse = [
            'data' => [],
            'pagination' => [
                'total' => 0,
                'page' => 1,
                'limit' => 10,
                'offset' => 0
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains', ['limit' => 10])
            ->willReturn($mockResponse);

        $result = $this->domainsApi->listDomains(['limit' => 10]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertEmpty($result['data']);
        $this->assertEquals(0, $result['pagination']->getTotal());
    }

    public function testSetupTracklink()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Tracking domain setup successfully',
            'data' => [
                'tracking_domain' => 'track.example.com',
                'cname_target' => 'track.kirim.email'
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/api/domains/example.com/setup-tracklink', ['tracking_domain' => 'track.example.com'])
            ->willReturn($mockResponse);

        $result = $this->domainsApi->setupTracklink('example.com', 'track.example.com');

        $this->assertTrue($result['success']);
        $this->assertEquals('track.example.com', $result['data']['tracking_domain']);
        $this->assertEquals('track.kirim.email', $result['data']['cname_target']);
    }

    public function testVerifyTracklink()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Tracking domain verification completed',
            'records' => [
                'cname' => true,
                'tracking_domain' => 'track.example.com'
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/api/domains/example.com/verify-tracklink')
            ->willReturn($mockResponse);

        $result = $this->domainsApi->verifyTracklink('example.com');

        $this->assertTrue($result['success']);
        $this->assertTrue($result['records']['cname']);
        $this->assertEquals('track.example.com', $result['records']['tracking_domain']);
    }

    public function testSetupTracklinkThrowsException()
    {
        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/api/domains/example.com/setup-tracklink', ['tracking_domain' => 'track.example.com'])
            ->willThrowException(new ApiException('Invalid tracking domain'));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Invalid tracking domain');

        $this->domainsApi->setupTracklink('example.com', 'track.example.com');
    }

    public function testVerifyTracklinkThrowsException()
    {
        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/api/domains/example.com/verify-tracklink')
            ->willThrowException(new ApiException('Tracking domain not configured'));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Tracking domain not configured');

        $this->domainsApi->verifyTracklink('example.com');
    }

    public function testListDomainsThrowsException()
    {
        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains', ['limit' => 10])
            ->willThrowException(new ApiException('API Error'));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('API Error');

        $this->domainsApi->listDomains(['limit' => 10]);
    }
}