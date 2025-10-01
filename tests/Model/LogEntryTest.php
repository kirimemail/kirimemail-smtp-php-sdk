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
            'id' => 'uuid-123',
            'user_guid' => 'user-guid-123',
            'user_domain_guid' => 'domain-guid-456',
            'user_smtp_guid' => 'smtp-guid-789',
            'webhook_guid' => 'webhook-guid-abc',
            'message_guid' => 'msg-guid-789',
            'server_message_guid' => 'server-msg-guid-def',
            'type' => 'email',
            'sender' => 'sender@example.com',
            'sender_domain' => 'example.com',
            'sender_ip' => '192.168.1.1',
            'recipient' => 'recipient@example.com',
            'recipient_domain' => 'example.com',
            'recipient_ip' => '192.168.1.2',
            'recipient_hash' => 'hash123',
            'server' => 'smtp.example.com',
            'event_type' => LogEntry::SMTP_EVENT_DELIVERED,
            'event' => '250',
            'event_detail' => '250 2.0.0 OK: queued as 12345',
            'tags' => 'tag1,tag2',
            'subject' => 'Test Subject',
            'created_at' => 1640995200,
            'sending_at' => 1640995300,
            'delivered_at' => 1640995400,
            'in_date' => 20220101,
            'in_date_hour' => 2022010100,
            'in_year_week' => 202201,
            'in_year_month' => 202201,
            'in_year' => 2022
        ];

        $logEntry = new LogEntry($data);

        $this->assertEquals('uuid-123', $logEntry->getId());
        $this->assertEquals('user-guid-123', $logEntry->getUserGuid());
        $this->assertEquals('domain-guid-456', $logEntry->getUserDomainGuid());
        $this->assertEquals('smtp-guid-789', $logEntry->getUserSmtpGuid());
        $this->assertEquals('webhook-guid-abc', $logEntry->getWebhookGuid());
        $this->assertEquals('msg-guid-789', $logEntry->getMessageGuid());
        $this->assertEquals('server-msg-guid-def', $logEntry->getServerMessageGuid());
        $this->assertEquals('email', $logEntry->getType());
        $this->assertEquals('sender@example.com', $logEntry->getSender());
        $this->assertEquals('example.com', $logEntry->getSenderDomain());
        $this->assertEquals('192.168.1.1', $logEntry->getSenderIp());
        $this->assertEquals('recipient@example.com', $logEntry->getRecipient());
        $this->assertEquals('example.com', $logEntry->getRecipientDomain());
        $this->assertEquals('192.168.1.2', $logEntry->getRecipientIp());
        $this->assertEquals('hash123', $logEntry->getRecipientHash());
        $this->assertEquals('smtp.example.com', $logEntry->getServer());
        $this->assertEquals(LogEntry::SMTP_EVENT_DELIVERED, $logEntry->getEventType());
        $this->assertEquals('250', $logEntry->getEvent());
        $this->assertEquals('250 2.0.0 OK: queued as 12345', $logEntry->getEventDetail());
        $this->assertEquals('tag1,tag2', $logEntry->getTags());
        $this->assertEquals('Test Subject', $logEntry->getSubject());
        $this->assertEquals(1640995200, $logEntry->getCreatedAt());
        $this->assertEquals(1640995300, $logEntry->getSendingAt());
        $this->assertEquals(1640995400, $logEntry->getDeliveredAt());
        $this->assertEquals(20220101, $logEntry->getInDate());
        $this->assertEquals(2022010100, $logEntry->getInDateHour());
        $this->assertEquals(202201, $logEntry->getInYearWeek());
        $this->assertEquals(202201, $logEntry->getInYearMonth());
        $this->assertEquals(2022, $logEntry->getInYear());
    }

    public function testLogEntryWithNullValues()
    {
        $logEntry = new LogEntry();

        $this->assertNull($logEntry->getId());
        $this->assertNull($logEntry->getUserGuid());
        $this->assertNull($logEntry->getUserDomainGuid());
        $this->assertNull($logEntry->getEventType());
        $this->assertNull($logEntry->getMessageGuid());
        $this->assertNull($logEntry->getCreatedAt());
    }

    public function testLogEntrySetters()
    {
        $logEntry = new LogEntry();

        $logEntry->setId('uuid-456')
                 ->setUserGuid('user-guid-789')
                 ->setUserDomainGuid('domain-guid-012')
                 ->setEventType(LogEntry::SMTP_EVENT_BOUNCED)
                 ->setMessageGuid('msg-guid-345')
                 ->setEvent('550')
                 ->setEventDetail('550 5.1.1 User unknown')
                 ->setCreatedAt(1640995300);

        $this->assertEquals('uuid-456', $logEntry->getId());
        $this->assertEquals('user-guid-789', $logEntry->getUserGuid());
        $this->assertEquals('domain-guid-012', $logEntry->getUserDomainGuid());
        $this->assertEquals(LogEntry::SMTP_EVENT_BOUNCED, $logEntry->getEventType());
        $this->assertEquals('msg-guid-345', $logEntry->getMessageGuid());
        $this->assertEquals('550', $logEntry->getEvent());
        $this->assertEquals('550 5.1.1 User unknown', $logEntry->getEventDetail());
        $this->assertEquals(1640995300, $logEntry->getCreatedAt());
    }

    public function testLogEntryToArray()
    {
        $data = [
            'id' => 'uuid-array',
            'user_guid' => 'array-user-guid',
            'user_domain_guid' => 'array-domain-guid',
            'user_smtp_guid' => null,
            'webhook_guid' => null,
            'message_guid' => 'array-msg-guid',
            'server_message_guid' => null,
            'type' => null,
            'sender' => null,
            'sender_domain' => null,
            'sender_ip' => null,
            'recipient' => null,
            'recipient_domain' => null,
            'recipient_ip' => null,
            'recipient_hash' => null,
            'server' => null,
            'event_type' => LogEntry::SMTP_EVENT_OPENED,
            'event' => null,
            'event_detail' => null,
            'tags' => null,
            'subject' => null,
            'created_at' => 1640995400,
            'sending_at' => null,
            'delivered_at' => null,
            'in_date' => null,
            'in_date_hour' => null,
            'in_year_week' => null,
            'in_year_month' => null,
            'in_year' => null,
        ];

        $logEntry = new LogEntry($data);
        $array = $logEntry->toArray();

        $this->assertEquals($data, $array);
    }

    public function testLogEntryJsonSerialization()
    {
        $data = [
            'id' => 'uuid-json',
            'user_guid' => 'json-user-guid',
            'user_domain_guid' => 'json-domain-guid',
            'user_smtp_guid' => null,
            'webhook_guid' => null,
            'message_guid' => 'json-msg-guid',
            'server_message_guid' => null,
            'type' => null,
            'sender' => null,
            'sender_domain' => null,
            'sender_ip' => null,
            'recipient' => null,
            'recipient_domain' => null,
            'recipient_ip' => null,
            'recipient_hash' => null,
            'server' => null,
            'event_type' => LogEntry::SMTP_EVENT_CLICKED,
            'event' => null,
            'event_detail' => null,
            'tags' => null,
            'subject' => null,
            'created_at' => 1640995500,
            'sending_at' => null,
            'delivered_at' => null,
            'in_date' => null,
            'in_date_hour' => null,
            'in_year_week' => null,
            'in_year_month' => null,
            'in_year' => null,
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

    public function testLogEntryGetCreatedAtDateTime()
    {
        $logEntry = new LogEntry(['created_at' => 1640995200]);
        $dateTime = $logEntry->getCreatedAtDateTime();

        $this->assertInstanceOf(\DateTime::class, $dateTime);
        $this->assertEquals('2022-01-01 00:00:00', $dateTime->format('Y-m-d H:i:s'));
    }

    public function testLogEntryGetCreatedAtDateTimeWithNullValue()
    {
        $logEntry = new LogEntry(['created_at' => null]);
        $dateTime = $logEntry->getCreatedAtDateTime();

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
        $this->assertNull($logEntry->getCreatedAt());
    }

    public function testLogEntryIdTypeAcceptsString()
    {
        $logEntry = new LogEntry(['id' => 'uuid-123']);
        $this->assertEquals('uuid-123', $logEntry->getId());
        $this->assertIsString($logEntry->getId());
    }

    public function testLogEntryIdTypeAcceptsNull()
    {
        $logEntry = new LogEntry(['id' => null]);
        $this->assertNull($logEntry->getId());
    }

    public function testLogEntrySetterWithStringId()
    {
        $logEntry = new LogEntry();
        $logEntry->setId('uuid-456');
        $this->assertEquals('uuid-456', $logEntry->getId());
        $this->assertIsString($logEntry->getId());
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