<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Tests\Model;

use PHPUnit\Framework\TestCase;
use KirimEmail\Smtp\Model\LogEntry;

class LogEntryTest extends TestCase
{
    public function testLogEntryConstructor()
    {
        $data = [
            'id' => 1,
            'user_guid' => 'user-guid-123',
            'user_domain_guid' => 'domain-guid-456',
            'event_type' => 'delivered',
            'message_guid' => 'msg-guid-789',
            'timestamp' => 1640995200
        ];

        $logEntry = new LogEntry($data);

        $this->assertEquals(1, $logEntry->getId());
        $this->assertEquals('user-guid-123', $logEntry->getUserGuid());
        $this->assertEquals('domain-guid-456', $logEntry->getUserDomainGuid());
        $this->assertEquals('delivered', $logEntry->getEventType());
        $this->assertEquals('msg-guid-789', $logEntry->getMessageGuid());
        $this->assertEquals(1640995200, $logEntry->getTimestamp());
    }

    public function testLogEntryWithNullValues()
    {
        $logEntry = new LogEntry([
            'id' => null,
            'user_guid' => null,
            'user_domain_guid' => null,
            'event_type' => null,
            'message_guid' => null,
            'timestamp' => null
        ]);

        $this->assertNull($logEntry->getId());
        $this->assertNull($logEntry->getUserGuid());
        $this->assertNull($logEntry->getUserDomainGuid());
        $this->assertNull($logEntry->getEventType());
        $this->assertNull($logEntry->getMessageGuid());
        $this->assertNull($logEntry->getTimestamp());
    }

    public function testLogEntrySetters()
    {
        $logEntry = new LogEntry();

        $logEntry->setId(5)
                 ->setUserGuid('user-guid-789')
                 ->setUserDomainGuid('domain-guid-012')
                 ->setEventType('bounced')
                 ->setMessageGuid('msg-guid-345')
                 ->setTimestamp(1640995300);

        $this->assertEquals(5, $logEntry->getId());
        $this->assertEquals('user-guid-789', $logEntry->getUserGuid());
        $this->assertEquals('domain-guid-012', $logEntry->getUserDomainGuid());
        $this->assertEquals('bounced', $logEntry->getEventType());
        $this->assertEquals('msg-guid-345', $logEntry->getMessageGuid());
        $this->assertEquals(1640995300, $logEntry->getTimestamp());
    }

    public function testLogEntryToArray()
    {
        $data = [
            'id' => 3,
            'user_guid' => 'array-user-guid',
            'user_domain_guid' => 'array-domain-guid',
            'event_type' => 'opened',
            'message_guid' => 'array-msg-guid',
            'timestamp' => 1640995400
        ];

        $logEntry = new LogEntry($data);
        $array = $logEntry->toArray();

        $this->assertEquals($data, $array);
    }

    public function testLogEntryJsonSerialization()
    {
        $data = [
            'id' => 4,
            'user_guid' => 'json-user-guid',
            'user_domain_guid' => 'json-domain-guid',
            'event_type' => 'clicked',
            'message_guid' => 'json-msg-guid',
            'timestamp' => 1640995500
        ];

        $logEntry = new LogEntry($data);
        $json = json_encode($logEntry);
        $decoded = json_decode($json, true);

        $this->assertEquals($data, $decoded);
    }

    public function testLogEntryEventTypeCheckers()
    {
        $sentLog = new LogEntry(['event_type' => 'sent']);
        $deliveredLog = new LogEntry(['event_type' => 'delivered']);
        $bouncedLog = new LogEntry(['event_type' => 'bounced']);
        $openedLog = new LogEntry(['event_type' => 'opened']);
        $clickedLog = new LogEntry(['event_type' => 'clicked']);
        $failedLog = new LogEntry(['event_type' => 'failed']);
        $otherLog = new LogEntry(['event_type' => 'unknown']);

        $this->assertTrue($sentLog->isSent());
        $this->assertFalse($sentLog->isDelivered());
        $this->assertFalse($sentLog->isBounced());
        $this->assertFalse($sentLog->isOpened());
        $this->assertFalse($sentLog->isClicked());
        $this->assertFalse($sentLog->isFailed());

        $this->assertTrue($deliveredLog->isDelivered());
        $this->assertFalse($deliveredLog->isSent());

        $this->assertTrue($bouncedLog->isBounced());
        $this->assertFalse($bouncedLog->isDelivered());

        $this->assertTrue($openedLog->isOpened());
        $this->assertFalse($openedLog->isBounced());

        $this->assertTrue($clickedLog->isClicked());
        $this->assertFalse($clickedLog->isOpened());

        $this->assertTrue($failedLog->isFailed());
        $this->assertFalse($failedLog->isClicked());

        $this->assertFalse($otherLog->isSent());
        $this->assertFalse($otherLog->isDelivered());
        $this->assertFalse($otherLog->isBounced());
        $this->assertFalse($otherLog->isOpened());
        $this->assertFalse($otherLog->isClicked());
        $this->assertFalse($otherLog->isFailed());
    }

    public function testLogEntryGetTimestampDateTime()
    {
        $logEntry = new LogEntry(['timestamp' => 1640995200]);
        $dateTime = $logEntry->getTimestampDateTime();

        $this->assertInstanceOf(\DateTime::class, $dateTime);
        $this->assertEquals('2022-01-01 00:00:00', $dateTime->format('Y-m-d H:i:s'));
    }

    public function testLogEntryGetTimestampDateTimeWithNullValue()
    {
        $logEntry = new LogEntry(['timestamp' => null]);
        $dateTime = $logEntry->getTimestampDateTime();

        $this->assertNull($dateTime);
    }

    public function testLogEntryWithEmptyData()
    {
        $logEntry = new LogEntry();

        $this->assertNull($logEntry->getId());
        $this->assertNull($logEntry->getUserGuid());
        $this->assertNull($logEntry->getUserDomainGuid());
        $this->assertNull($logEntry->getEventType());
        $this->assertNull($logEntry->getMessageGuid());
        $this->assertNull($logEntry->getTimestamp());
    }

    public function testLogEntryIdTypeAcceptsInteger()
    {
        $logEntry = new LogEntry(['id' => 123]);
        $this->assertEquals(123, $logEntry->getId());
        $this->assertIsInt($logEntry->getId());
    }

    public function testLogEntryIdTypeAcceptsNull()
    {
        $logEntry = new LogEntry(['id' => null]);
        $this->assertNull($logEntry->getId());
    }

    public function testLogEntrySetterWithIntegerId()
    {
        $logEntry = new LogEntry();
        $logEntry->setId(456);
        $this->assertEquals(456, $logEntry->getId());
        $this->assertIsInt($logEntry->getId());
    }
}