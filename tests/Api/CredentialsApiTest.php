<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Tests\Api;

use PHPUnit\Framework\TestCase;
use KirimEmail\Smtp\Client\SmtpClient;
use KirimEmail\Smtp\Api\CredentialsApi;
use KirimEmail\Smtp\Model\Credential;
use KirimEmail\Smtp\Model\Pagination;
use KirimEmail\Smtp\Exception\ApiException;

class CredentialsApiTest extends TestCase
{
    private SmtpClient $mockClient;
    private CredentialsApi $credentialsApi;

    protected function setUp(): void
    {
        $this->mockClient = $this->createMock(SmtpClient::class);
        $this->credentialsApi = new CredentialsApi($this->mockClient);
    }

    public function testListCredentials()
    {
        $mockResponse = [
            'data' => [
                'data' => [
                    [
                        'id' => 1,
                        'user_smtp_guid' => 'smtp-guid-123',
                        'username' => 'test@example.com',
                        'is_verified' => false,
                        'status' => false,
                        'is_deleted' => false,
                        'created_at' => 1640995200,
                        'modified_at' => 0,
                        'deleted_at' => null,
                        'last_password_changed' => 1640995200
                    ]
                ],
                'pagination' => [
                    'total' => 1,
                    'page' => 1,
                    'limit' => 15,
                    'offset' => 0
                ]
            ],
            'domain' => 'example.com'
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/credentials', ['limit' => 10])
            ->willReturn($mockResponse);

        $result = $this->credentialsApi->listCredentials('example.com', ['limit' => 10]);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('domain', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertCount(1, $result['data']);
        $this->assertInstanceOf(Credential::class, $result['data'][0]);
        $this->assertEquals('example.com', $result['domain']);
        $this->assertEquals('test@example.com', $result['data'][0]->getUsername());
    }

    public function testCreateCredential()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Credential created successfully.',
            'data' => [
                'credential' => [
                    'id' => 2,
                    'user_smtp_guid' => 'smtp-guid-456',
                    'username' => 'newuser@example.com',
                    'created_at' => 1640995200,
                    'modified_at' => 1640995200
                ],
                'password' => 'generated-password-123',
                'remote_synced' => true
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/api/domains/example.com/credentials', ['username' => 'newuser'])
            ->willReturn($mockResponse);

        $result = $this->credentialsApi->createCredential('example.com', 'newuser');

        $this->assertTrue($result['success']);
        $this->assertEquals('Credential created successfully.', $result['message']);
        $this->assertInstanceOf(Credential::class, $result['data']['credential']);
        $this->assertEquals('generated-password-123', $result['data']['credential']->getPassword());
        $this->assertTrue($result['data']['credential']->isRemoteSynced());
    }

    public function testGetCredential()
    {
        $mockResponse = [
            'success' => true,
            'data' => [
                'id' => 3,
                'user_smtp_guid' => 'smtp-guid-789',
                'username' => 'existing@example.com',
                'is_verified' => true,
                'status' => true,
                'created_at' => 1640995200,
                'modified_at' => 1640995300
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/credentials/3')
            ->willReturn($mockResponse);

        $result = $this->credentialsApi->getCredential('example.com', '3');

        $this->assertTrue($result['success']);
        $this->assertInstanceOf(Credential::class, $result['data']);
        $this->assertEquals('existing@example.com', $result['data']->getUsername());
        $this->assertTrue($result['data']->isVerified());
    }

    public function testDeleteCredential()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Credential deleted successfully'
        ];

        $this->mockClient->expects($this->once())
            ->method('delete')
            ->with('/api/domains/example.com/credentials/4')
            ->willReturn($mockResponse);

        $result = $this->credentialsApi->deleteCredential('example.com', '4');

        $this->assertTrue($result['success']);
        $this->assertEquals('Credential deleted successfully', $result['message']);
    }

    public function testResetPassword()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Password reset successfully.',
            'data' => [
                'credential' => [
                    'id' => 5,
                    'user_smtp_guid' => 'smtp-guid-reset',
                    'username' => 'resetuser@example.com',
                    'created_at' => 1640995200,
                    'modified_at' => 1640995400
                ],
                'new_password' => 'new-generated-password',
                'strength_info' => ['score' => 8, 'feedback' => 'Strong'],
                'remote_synced' => true
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('put')
            ->with('/api/domains/example.com/credentials/5/reset-password')
            ->willReturn($mockResponse);

        $result = $this->credentialsApi->resetPassword('example.com', '5');

        $this->assertTrue($result['success']);
        $this->assertEquals('Password reset successfully.', $result['message']);
        $this->assertInstanceOf(Credential::class, $result['data']['credential']);
        $this->assertEquals('new-generated-password', $result['data']['credential']->getPassword());
        $this->assertEquals(['score' => 8, 'feedback' => 'Strong'], $result['data']['credential']->getStrengthInfo());
        $this->assertTrue($result['data']['credential']->isRemoteSynced());
    }

    public function testListCredentialsWithNestedStructure()
    {
        $mockResponse = [
            'data' => [
                'current_page' => 1,
                'data' => [
                    [
                        'id' => 6,
                        'user_smtp_guid' => 'smtp-guid-nested',
                        'username' => 'nested@example.com',
                        'is_verified' => true,
                        'created_at' => 1640995200
                    ]
                ],
                'total' => 1
            ],
            'domain' => 'example.com'
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/credentials')
            ->willReturn($mockResponse);

        $result = $this->credentialsApi->listCredentials('example.com');

        $this->assertIsArray($result);
        $this->assertCount(1, $result['data']);
        $this->assertEquals('nested@example.com', $result['data'][0]->getUsername());
    }

    public function testListCredentialsWithEmptyResponse()
    {
        $mockResponse = [
            'data' => [
                'data' => [],
                'pagination' => [
                    'total' => 0,
                    'page' => 1,
                    'limit' => 15,
                    'offset' => 0
                ]
            ],
            'domain' => 'example.com'
        ];

        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/credentials')
            ->willReturn($mockResponse);

        $result = $this->credentialsApi->listCredentials('example.com');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('domain', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertEmpty($result['data']);
        $this->assertEquals('example.com', $result['domain']);
    }

    public function testCreateCredentialWithoutOptionalFields()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Credential created successfully.',
            'data' => [
                'credential' => [
                    'id' => 7,
                    'user_smtp_guid' => 'smtp-guid-minimal',
                    'username' => 'minimal@example.com',
                    'created_at' => 1640995200,
                    'modified_at' => 1640995200
                ]
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/api/domains/example.com/credentials', ['username' => 'minimal'])
            ->willReturn($mockResponse);

        $result = $this->credentialsApi->createCredential('example.com', 'minimal');

        $this->assertTrue($result['success']);
        $this->assertInstanceOf(Credential::class, $result['data']['credential']);
        $this->assertEquals('minimal@example.com', $result['data']['credential']->getUsername());
        $this->assertNull($result['data']['credential']->getPassword());
    }

    public function testResetPasswordWithoutOptionalFields()
    {
        $mockResponse = [
            'success' => true,
            'message' => 'Password reset successfully.',
            'data' => [
                'credential' => [
                    'id' => 8,
                    'user_smtp_guid' => 'smtp-guid-reset-min',
                    'username' => 'resetmin@example.com',
                    'created_at' => 1640995200,
                    'modified_at' => 1640995400
                ],
                'new_password' => 'new-password'
            ]
        ];

        $this->mockClient->expects($this->once())
            ->method('put')
            ->with('/api/domains/example.com/credentials/8/reset-password')
            ->willReturn($mockResponse);

        $result = $this->credentialsApi->resetPassword('example.com', '8');

        $this->assertTrue($result['success']);
        $this->assertInstanceOf(Credential::class, $result['data']['credential']);
        $this->assertEquals('new-password', $result['data']['credential']->getPassword());
        $this->assertNull($result['data']['credential']->getStrengthInfo());
    }

    public function testListCredentialsThrowsException()
    {
        $this->mockClient->expects($this->once())
            ->method('get')
            ->with('/api/domains/example.com/credentials')
            ->willThrowException(new ApiException('API Error'));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('API Error');

        $this->credentialsApi->listCredentials('example.com');
    }

    public function testCreateCredentialThrowsException()
    {
        $this->mockClient->expects($this->once())
            ->method('post')
            ->with('/api/domains/example.com/credentials', ['username' => 'test'])
            ->willThrowException(new ApiException('Creation failed'));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Creation failed');

        $this->credentialsApi->createCredential('example.com', 'test');
    }

    public function testResetPasswordThrowsException()
    {
        $this->mockClient->expects($this->once())
            ->method('put')
            ->with('/api/domains/example.com/credentials/1/reset-password')
            ->willThrowException(new ApiException('Reset failed'));

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Reset failed');

        $this->credentialsApi->resetPassword('example.com', '1');
    }
}