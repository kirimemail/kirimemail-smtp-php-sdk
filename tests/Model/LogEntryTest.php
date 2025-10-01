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
            'event_type' => LogEntry::SMTP_EVENT_DELIVERED,
            'message_guid' => 'msg-guid-789',
            'timestamp' => 1640995200
        ];

        $logEntry = new LogEntry($data);

        $this->assertEquals(1, $logEntry->getId());
        $this->assertEquals('user-guid-123', $logEntry->getUserGuid());
        $this->assertEquals('domain-guid-456', $logEntry->getUserDomainGuid());
        $this->assertEquals(LogEntry::SMTP_EVENT_DELIVERED, $logEntry->getEventType());
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
                 ->setEventType(LogEntry::SMTP_EVENT_BOUNCED)
                 ->setMessageGuid('msg-guid-345')
                 ->setTimestamp(1640995300);

        $this->assertEquals(5, $logEntry->getId());
        $this->assertEquals('user-guid-789', $logEntry->getUserGuid());
        $this->assertEquals('domain-guid-012', $logEntry->getUserDomainGuid());
        $this->assertEquals(LogEntry::SMTP_EVENT_BOUNCED, $logEntry->getEventType());
        $this->assertEquals('msg-guid-345', $logEntry->getMessageGuid());
        $this->assertEquals(1640995300, $logEntry->getTimestamp());
    }

    public function testLogEntryToArray()
    {
        $data = [
            'id' => 3,
            'user_guid' => 'array-user-guid',
            'user_domain_guid' => 'array-domain-guid',
            'event_type' => LogEntry::SMTP_EVENT_OPENED,
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
            'event_type' => LogEntry::SMTP_EVENT_CLICKED,
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
        $queuedLog = new LogEntry(['event_type' => LogEntry::SMTP_EVENT_QUEUED]);
        $sendLog = new LogEntry(['event_type' => LogEntry::SMTP_EVENT_SEND]);
        $deliveredLog = new LogEntry(['event_type' => LogEntry::SMTP_EVENT_DELIVERED]);
        $bouncedLog = new LogEntry(['event_type' => LogEntry::SMTP_EVENT_BOUNCED]);
        $failedLog = new LogEntry(['event_type' => LogEntry::SMTP_EVENT_FAILED]);
        $permanentFailLog = new LogEntry(['event_type' => LogEntry::SMTP_EVENT_PERMANENT_FAIL]);
        $openedLog = new LogEntry(['event_type' => LogEntry::SMTP_EVENT_OPENED]);
        $clickedLog = new LogEntry(['event_type' => LogEntry::SMTP_EVENT_CLICKED]);
        $unsubscribedLog = new LogEntry(['event_type' => LogEntry::SMTP_EVENT_UNSUBSCRIBED]);
        $tempFailureLog = new LogEntry(['event_type' => LogEntry::SMTP_EVENT_TEMP_FAILURE]);
        $deferredLog = new LogEntry(['event_type' => LogEntry::SMTP_EVENT_DEFERRED]);
        $otherLog = new LogEntry(['event_type' => 'unknown']);

        // Test each event type
        $this->assertTrue($queuedLog->isQueued());
        $this->assertFalse($queuedLog->isDelivered());
        $this->assertFalse($queuedLog->isBounced());
        $this->assertFalse($queuedLog->isOpened());
        $this->assertFalse($queuedLog->isClicked());
        $this->assertFalse($queuedLog->isFailed());

        $this->assertTrue($sendLog->isSend());
        $this->assertFalse($sendLog->isQueued());
        $this->assertFalse($sendLog->isDelivered());

        $this->assertTrue($deliveredLog->isDelivered());
        $this->assertFalse($deliveredLog->isQueued());
        $this->assertFalse($deliveredLog->isSend());

        $this->assertTrue($bouncedLog->isBounced());
        $this->assertFalse($bouncedLog->isDelivered());
        $this->assertFalse($bouncedLog->isFailed());

        $this->assertTrue($failedLog->isFailed());
        $this->assertFalse($failedLog->isBounced());
        $this->assertFalse($failedLog->isPermanentFail());

        $this->assertTrue($permanentFailLog->isPermanentFail());
        $this->assertFalse($permanentFailLog->isFailed());
        $this->assertFalse($permanentFailLog->isTempFailure());

        $this->assertTrue($openedLog->isOpened());
        $this->assertFalse($openedLog->isBounced());
        $this->assertFalse($openedLog->isClicked());

        $this->assertTrue($clickedLog->isClicked());
        $this->assertFalse($clickedLog->isOpened());
        $this->assertFalse($clickedLog->isUnsubscribed());

        $this->assertTrue($unsubscribedLog->isUnsubscribed());
        $this->assertFalse($unsubscribedLog->isClicked());
        $this->assertFalse($unsubscribedLog->isOpened());

        $this->assertTrue($tempFailureLog->isTempFailure());
        $this->assertFalse($tempFailureLog->isFailed());
        $this->assertFalse($tempFailureLog->isPermanentFail());

        $this->assertTrue($deferredLog->isDeferred());
        $this->assertFalse($deferredLog->isTempFailure());
        $this->assertFalse($deferredLog->isQueued());

        // Test unknown event type
        $this->assertFalse($otherLog->isQueued());
        $this->assertFalse($otherLog->isSend());
        $this->assertFalse($otherLog->isDelivered());
        $this->assertFalse($otherLog->isBounced());
        $this->assertFalse($otherLog->isFailed());
        $this->assertFalse($otherLog->isPermanentFail());
        $this->assertFalse($otherLog->isOpened());
        $this->assertFalse($otherLog->isClicked());
        $this->assertFalse($otherLog->isUnsubscribed());
        $this->assertFalse($otherLog->isTempFailure());
        $this->assertFalse($otherLog->isDeferred());
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

    public function testLogEntryEventConstants()
    {
        $this->assertEquals('queued', LogEntry::SMTP_EVENT_QUEUED);
        $this->assertEquals('send', LogEntry::SMTP_EVENT_SEND);
        $this->assertEquals('delivered', LogEntry::SMTP_EVENT_DELIVERED);
        $this->assertEquals('bounced', LogEntry::SMTP_EVENT_BOUNCED);
        $this->assertEquals('failed', LogEntry::SMTP_EVENT_FAILED);
        $this->assertEquals('permanent_fail', LogEntry::SMTP_EVENT_PERMANENT_FAIL);
        $this->assertEquals('opened', LogEntry::SMTP_EVENT_OPENED);
        $this->assertEquals('clicked', LogEntry::SMTP_EVENT_CLICKED);
        $this->assertEquals('unsubscribed', LogEntry::SMTP_EVENT_UNSUBSCRIBED);
        $this->assertEquals('temp_fail', LogEntry::SMTP_EVENT_TEMP_FAILURE);
        $this->assertEquals('deferred', LogEntry::SMTP_EVENT_DEFERRED);
    }
}