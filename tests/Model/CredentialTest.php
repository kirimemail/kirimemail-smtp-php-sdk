<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Tests\Model;

use PHPUnit\Framework\TestCase;
use KirimEmail\Smtp\Model\Credential;

class CredentialTest extends TestCase
{
    public function testCredentialCreationWithBasicData()
    {
        $data = [
            'id' => 1,
            'user_smtp_guid' => 'smtp-guid-123',
            'username' => 'test@example.com',
            'created_at' => 1640995200,
            'modified_at' => 1640995200
        ];

        $credential = new Credential($data);

        $this->assertEquals(1, $credential->getId());
        $this->assertEquals('smtp-guid-123', $credential->getUserSmtpGuid());
        $this->assertEquals('test@example.com', $credential->getUsername());
        $this->assertEquals(1640995200, $credential->getCreatedAt());
        $this->assertEquals(1640995200, $credential->getModifiedAt());
        $this->assertNull($credential->getPassword());
        $this->assertNull($credential->isVerified());
        $this->assertNull($credential->getStatus());
    }

    public function testCredentialCreationWithCompleteData()
    {
        $data = [
            'id' => 2,
            'user_smtp_guid' => 'smtp-guid-456',
            'username' => 'user@domain.com',
            'is_verified' => true,
            'status' => false,
            'is_deleted' => false,
            'created_at' => 1640995200,
            'modified_at' => 1640995300,
            'deleted_at' => null,
            'last_password_changed' => 1640995250,
            'password' => 'generated-password',
            'strength_info' => ['score' => 8, 'feedback' => 'Strong password'],
            'remote_synced' => true
        ];

        $credential = new Credential($data);

        $this->assertEquals(2, $credential->getId());
        $this->assertEquals('smtp-guid-456', $credential->getUserSmtpGuid());
        $this->assertEquals('user@domain.com', $credential->getUsername());
        $this->assertTrue($credential->isVerified());
        $this->assertFalse($credential->getStatus());
        $this->assertFalse($credential->isDeleted());
        $this->assertEquals(1640995200, $credential->getCreatedAt());
        $this->assertEquals(1640995300, $credential->getModifiedAt());
        $this->assertNull($credential->getDeletedAt());
        $this->assertEquals(1640995250, $credential->getLastPasswordChanged());
        $this->assertEquals('generated-password', $credential->getPassword());
        $this->assertEquals(['score' => 8, 'feedback' => 'Strong password'], $credential->getStrengthInfo());
        $this->assertTrue($credential->isRemoteSynced());
    }

    public function testCredentialDateTimeHelpers()
    {
        $timestamp = 1640995200; // 2022-01-01 00:00:00 UTC
        $data = [
            'id' => 1,
            'user_smtp_guid' => 'test-guid',
            'username' => 'test@example.com',
            'created_at' => $timestamp,
            'modified_at' => $timestamp,
            'deleted_at' => null,
            'last_password_changed' => $timestamp
        ];

        $credential = new Credential($data);

        $createdAt = $credential->getCreatedAtDateTime();
        $modifiedAt = $credential->getModifiedAtDateTime();
        $deletedAt = $credential->getDeletedAtDateTime();
        $lastChangedAt = $credential->getLastPasswordChangedDateTime();

        $this->assertInstanceOf(\DateTime::class, $createdAt);
        $this->assertInstanceOf(\DateTime::class, $modifiedAt);
        $this->assertInstanceOf(\DateTime::class, $lastChangedAt);
        $this->assertNull($deletedAt);

        $this->assertEquals('2022-01-01 00:00:00', $createdAt->format('Y-m-d H:i:s'));
        $this->assertEquals('2022-01-01 00:00:00', $modifiedAt->format('Y-m-d H:i:s'));
        $this->assertEquals('2022-01-01 00:00:00', $lastChangedAt->format('Y-m-d H:i:s'));
    }

    public function testCredentialSetters()
    {
        $credential = new Credential();

        $credential->setId(10)
                  ->setUserSmtpGuid('new-guid')
                  ->setUsername('newuser@example.com')
                  ->setVerified(true)
                  ->setStatus(true)
                  ->setDeletedAt(null)
                  ->setIsDeleted(false)
                  ->setCreatedAt(1640995200)
                  ->setModifiedAt(1640995300)
                  ->setDeletedAt(null)
                  ->setLastPasswordChanged(1640995250)
                  ->setPassword('secret-password')
                  ->setStrengthInfo(['score' => 10])
                  ->setRemoteSynced(true);

        $this->assertEquals(10, $credential->getId());
        $this->assertEquals('new-guid', $credential->getUserSmtpGuid());
        $this->assertEquals('newuser@example.com', $credential->getUsername());
        $this->assertTrue($credential->isVerified());
        $this->assertTrue($credential->getStatus());
        $this->assertFalse($credential->isDeleted());
        $this->assertEquals('secret-password', $credential->getPassword());
        $this->assertEquals(['score' => 10], $credential->getStrengthInfo());
        $this->assertTrue($credential->isRemoteSynced());
    }

    public function testCredentialToArray()
    {
        $data = [
            'id' => 5,
            'user_smtp_guid' => 'array-test-guid',
            'username' => 'arraytest@example.com',
            'is_verified' => false,
            'status' => true,
            'is_deleted' => false,
            'created_at' => 1640995200,
            'modified_at' => 1640995300,
            'deleted_at' => null,
            'last_password_changed' => 1640995250,
            'password' => 'array-password',
            'strength_info' => ['score' => 7],
            'remote_synced' => false
        ];

        $credential = new Credential($data);
        $array = $credential->toArray();

        $this->assertEquals($data, $array);
    }

    public function testCredentialJsonSerialization()
    {
        $data = [
            'id' => 8,
            'user_smtp_guid' => 'json-test-guid',
            'username' => 'jsontest@example.com',
            'is_verified' => true,
            'status' => false,
            'is_deleted' => null,
            'created_at' => 1640995200,
            'modified_at' => null,
            'deleted_at' => null,
            'last_password_changed' => null,
            'password' => 'json-password',
            'strength_info' => null,
            'remote_synced' => null
        ];

        $credential = new Credential($data);
        $json = json_encode($credential);
        $decoded = json_decode($json, true);

        $this->assertEquals($data, $decoded);
    }

    public function testCredentialWithNullValues()
    {
        $data = [
            'id' => null,
            'user_smtp_guid' => null,
            'username' => null,
            'is_verified' => null,
            'status' => null,
            'is_deleted' => null,
            'created_at' => null,
            'modified_at' => null,
            'deleted_at' => null,
            'last_password_changed' => null,
            'password' => null,
            'strength_info' => null,
            'remote_synced' => null
        ];

        $credential = new Credential($data);

        $this->assertNull($credential->getId());
        $this->assertNull($credential->getUserSmtpGuid());
        $this->assertNull($credential->getUsername());
        $this->assertNull($credential->isVerified());
        $this->assertNull($credential->getStatus());
        $this->assertNull($credential->isDeleted());
        $this->assertNull($credential->getCreatedAt());
        $this->assertNull($credential->getModifiedAt());
        $this->assertNull($credential->getDeletedAt());
        $this->assertNull($credential->getLastPasswordChanged());
        $this->assertNull($credential->getPassword());
        $this->assertNull($credential->getStrengthInfo());
        $this->assertNull($credential->isRemoteSynced());
    }

    public function testCredentialCreateResponseSimulation()
    {
        // Simulate API response from credential creation
        $apiResponse = [
            'success' => true,
            'message' => 'Credential created successfully.',
            'data' => [
                'credential' => [
                    'id' => 15,
                    'user_smtp_guid' => 'created-cred-guid',
                    'username' => 'newuser@domain.com',
                    'created_at' => 1640995200,
                    'modified_at' => 1640995200
                ],
                'password' => 'newly-generated-password',
                'remote_synced' => true
            ]
        ];

        // Create credential from the nested structure
        $credential = new Credential($apiResponse['data']['credential']);

        // Add the password and remote_synced from the response
        $credential->setPassword($apiResponse['data']['password']);
        $credential->setRemoteSynced($apiResponse['data']['remote_synced']);

        $this->assertEquals(15, $credential->getId());
        $this->assertEquals('created-cred-guid', $credential->getUserSmtpGuid());
        $this->assertEquals('newuser@domain.com', $credential->getUsername());
        $this->assertEquals('newly-generated-password', $credential->getPassword());
        $this->assertTrue($credential->isRemoteSynced());
    }

    public function testCredentialPasswordResetSimulation()
    {
        // Simulate API response from password reset
        $apiResponse = [
            'success' => true,
            'message' => 'Password reset successfully.',
            'data' => [
                'credential' => [
                    'id' => 20,
                    'user_smtp_guid' => 'reset-cred-guid',
                    'username' => 'existinguser@domain.com',
                    'created_at' => 1640995200,
                    'modified_at' => 1640995300
                ],
                'new_password' => 'reset-generated-password',
                'strength_info' => ['score' => 9, 'feedback' => 'Very strong'],
                'remote_synced' => true
            ]
        ];

        // Create credential from the nested structure
        $credential = new Credential($apiResponse['data']['credential']);

        // Add the new_password to the password property for consistency
        $credential->setPassword($apiResponse['data']['new_password']);
        $credential->setStrengthInfo($apiResponse['data']['strength_info']);
        $credential->setRemoteSynced($apiResponse['data']['remote_synced']);

        $this->assertEquals(20, $credential->getId());
        $this->assertEquals('reset-cred-guid', $credential->getUserSmtpGuid());
        $this->assertEquals('existinguser@domain.com', $credential->getUsername());
        $this->assertEquals('reset-generated-password', $credential->getPassword());
        $this->assertEquals(['score' => 9, 'feedback' => 'Very strong'], $credential->getStrengthInfo());
        $this->assertTrue($credential->isRemoteSynced());
    }
}