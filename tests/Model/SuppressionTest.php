<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Tests\Model;

use PHPUnit\Framework\TestCase;
use KirimEmail\Smtp\Model\Suppression;

class SuppressionTest extends TestCase
{
    public function testSuppressionConstructor()
    {
        $data = [
            'id' => 1,
            'type' => 'bounce',
            'recipient_type' => 'email',
            'recipient' => 'bounced@example.com',
            'description' => 'Hard bounce',
            'source' => 'auto',
            'created_at' => 1640995200,
            'modified_at' => 1640995300
        ];

        $suppression = new Suppression($data);

        $this->assertEquals(1, $suppression->getId());
        $this->assertEquals('bounce', $suppression->getType());
        $this->assertEquals('email', $suppression->getRecipientType());
        $this->assertEquals('bounced@example.com', $suppression->getRecipient());
        $this->assertEquals('Hard bounce', $suppression->getDescription());
        $this->assertEquals('auto', $suppression->getSource());
        $this->assertEquals(1640995200, $suppression->getCreatedAt());
        $this->assertEquals(1640995300, $suppression->getModifiedAt());
    }

    public function testSuppressionWithNullValues()
    {
        $suppression = new Suppression([
            'id' => null,
            'type' => null,
            'recipient_type' => null,
            'recipient' => null,
            'description' => null,
            'source' => null,
            'created_at' => null,
            'modified_at' => null
        ]);

        $this->assertNull($suppression->getId());
        $this->assertNull($suppression->getType());
        $this->assertNull($suppression->getRecipientType());
        $this->assertNull($suppression->getRecipient());
        $this->assertNull($suppression->getDescription());
        $this->assertNull($suppression->getSource());
        $this->assertNull($suppression->getCreatedAt());
        $this->assertNull($suppression->getModifiedAt());
    }

    public function testSuppressionSetters()
    {
        $suppression = new Suppression();

        $suppression->setId(5)
                   ->setType('unsubscribe')
                   ->setRecipientType('email')
                   ->setRecipient('unsub@example.com')
                   ->setDescription('User unsubscribed')
                   ->setSource('manual')
                   ->setCreatedAt(1640995400)
                   ->setModifiedAt(1640995500);

        $this->assertEquals(5, $suppression->getId());
        $this->assertEquals('unsubscribe', $suppression->getType());
        $this->assertEquals('email', $suppression->getRecipientType());
        $this->assertEquals('unsub@example.com', $suppression->getRecipient());
        $this->assertEquals('User unsubscribed', $suppression->getDescription());
        $this->assertEquals('manual', $suppression->getSource());
        $this->assertEquals(1640995400, $suppression->getCreatedAt());
        $this->assertEquals(1640995500, $suppression->getModifiedAt());
    }

    public function testSuppressionToArray()
    {
        $data = [
            'id' => 3,
            'type' => 'whitelist',
            'recipient_type' => 'email',
            'recipient' => 'whitelisted@example.com',
            'description' => 'Manually whitelisted',
            'source' => 'manual',
            'created_at' => 1640995600,
            'modified_at' => 1640995700
        ];

        $suppression = new Suppression($data);
        $array = $suppression->toArray();

        $this->assertEquals($data, $array);
    }

    public function testSuppressionJsonSerialization()
    {
        $data = [
            'id' => 4,
            'type' => 'bounce',
            'recipient_type' => 'email',
            'recipient' => 'hardbounce@example.com',
            'description' => 'Mailbox full',
            'source' => 'auto',
            'created_at' => 1640995800,
            'modified_at' => 1640995900
        ];

        $suppression = new Suppression($data);
        $json = json_encode($suppression);
        $decoded = json_decode($json, true);

        $this->assertEquals($data, $decoded);
    }

    public function testSuppressionTypeCheckers()
    {
        $bounceSuppression = new Suppression(['type' => 'bounce']);
        $unsubscribeSuppression = new Suppression(['type' => 'unsubscribe']);
        $whitelistSuppression = new Suppression(['type' => 'whitelist']);
        $otherSuppression = new Suppression(['type' => 'unknown']);

        $this->assertTrue($bounceSuppression->isBounce());
        $this->assertFalse($bounceSuppression->isUnsubscribe());
        $this->assertFalse($bounceSuppression->isWhitelist());

        $this->assertTrue($unsubscribeSuppression->isUnsubscribe());
        $this->assertFalse($unsubscribeSuppression->isBounce());
        $this->assertFalse($unsubscribeSuppression->isWhitelist());

        $this->assertTrue($whitelistSuppression->isWhitelist());
        $this->assertFalse($whitelistSuppression->isBounce());
        $this->assertFalse($whitelistSuppression->isUnsubscribe());

        $this->assertFalse($otherSuppression->isBounce());
        $this->assertFalse($otherSuppression->isUnsubscribe());
        $this->assertFalse($otherSuppression->isWhitelist());
    }

    public function testSuppressionGetCreatedAtDateTime()
    {
        $suppression = new Suppression(['created_at' => 1640995200]);
        $dateTime = $suppression->getCreatedAtDateTime();

        $this->assertInstanceOf(\DateTime::class, $dateTime);
        $this->assertEquals('2022-01-01 00:00:00', $dateTime->format('Y-m-d H:i:s'));
    }

    public function testSuppressionGetCreatedAtDateTimeWithNullValue()
    {
        $suppression = new Suppression(['created_at' => null]);
        $dateTime = $suppression->getCreatedAtDateTime();

        $this->assertNull($dateTime);
    }

    public function testSuppressionGetModifiedAtDateTime()
    {
        $suppression = new Suppression(['modified_at' => 1640995300]);
        $dateTime = $suppression->getModifiedAtDateTime();

        $this->assertInstanceOf(\DateTime::class, $dateTime);
        $this->assertEquals('2022-01-01 00:01:40', $dateTime->format('Y-m-d H:i:s'));
    }

    public function testSuppressionGetModifiedAtDateTimeWithNullValue()
    {
        $suppression = new Suppression(['modified_at' => null]);
        $dateTime = $suppression->getModifiedAtDateTime();

        $this->assertNull($dateTime);
    }

    public function testSuppressionWithEmptyData()
    {
        $suppression = new Suppression();

        $this->assertNull($suppression->getId());
        $this->assertNull($suppression->getType());
        $this->assertNull($suppression->getRecipientType());
        $this->assertNull($suppression->getRecipient());
        $this->assertNull($suppression->getDescription());
        $this->assertNull($suppression->getSource());
        $this->assertNull($suppression->getCreatedAt());
        $this->assertNull($suppression->getModifiedAt());
    }

    public function testSuppressionWithMinimalData()
    {
        $suppression = new Suppression([
            'type' => 'bounce',
            'recipient' => 'test@example.com'
        ]);

        $this->assertEquals('bounce', $suppression->getType());
        $this->assertEquals('test@example.com', $suppression->getRecipient());
        $this->assertNull($suppression->getId());
        $this->assertNull($suppression->getDescription());
    }
}