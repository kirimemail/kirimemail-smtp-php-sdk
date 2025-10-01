<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Model;

use JsonSerializable;

class LogEntry implements JsonSerializable
{
    // Event type constants
    public const SMTP_EVENT_QUEUED = 'queued';
    public const SMTP_EVENT_SEND = 'send';
    public const SMTP_EVENT_DELIVERED = 'delivered';
    public const SMTP_EVENT_BOUNCED = 'bounced';
    public const SMTP_EVENT_FAILED = 'failed';
    public const SMTP_EVENT_PERMANENT_FAIL = 'permanent_fail';
    public const SMTP_EVENT_OPENED = 'opened';
    public const SMTP_EVENT_CLICKED = 'clicked';
    public const SMTP_EVENT_UNSUBSCRIBED = 'unsubscribed';
    public const SMTP_EVENT_TEMP_FAILURE = 'temp_fail';
    public const SMTP_EVENT_DEFERRED = 'deferred';

    private ?string $id;
    private ?string $userGuid;
    private ?string $userDomainGuid;
    private ?string $userSmtpGuid;
    private ?string $webhookGuid;
    private ?string $messageGuid;
    private ?string $serverMessageGuid;
    private ?string $type;
    private ?string $sender;
    private ?string $senderDomain;
    private ?string $senderIp;
    private ?string $recipient;
    private ?string $recipientDomain;
    private ?string $recipientIp;
    private ?string $recipientHash;
    private ?string $server;
    private ?string $eventType;
    private ?string $event;
    private ?string $eventDetail;
    private ?string $tags;
    private ?string $subject;
    private ?int $createdAt;
    private ?int $sendingAt;
    private ?int $deliveredAt;
    private ?int $inDate;
    private ?int $inDateHour;
    private ?int $inYearWeek;
    private ?int $inYearMonth;
    private ?int $inYear;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->userGuid = $data['user_guid'] ?? null;
        $this->userDomainGuid = $data['user_domain_guid'] ?? null;
        $this->userSmtpGuid = $data['user_smtp_guid'] ?? null;
        $this->webhookGuid = $data['webhook_guid'] ?? null;
        $this->messageGuid = $data['message_guid'] ?? null;
        $this->serverMessageGuid = $data['server_message_guid'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->sender = $data['sender'] ?? null;
        $this->senderDomain = $data['sender_domain'] ?? null;
        $this->senderIp = $data['sender_ip'] ?? null;
        $this->recipient = $data['recipient'] ?? null;
        $this->recipientDomain = $data['recipient_domain'] ?? null;
        $this->recipientIp = $data['recipient_ip'] ?? null;
        $this->recipientHash = $data['recipient_hash'] ?? null;
        $this->server = $data['server'] ?? null;
        $this->eventType = $data['event_type'] ?? null;
        $this->event = $data['event'] ?? null;
        $this->eventDetail = $data['event_detail'] ?? null;
        $this->tags = $data['tags'] ?? null;
        $this->subject = $data['subject'] ?? null;
        $this->createdAt = isset($data['created_at']) ? (int)$data['created_at'] : null;
        $this->sendingAt = isset($data['sending_at']) ? (int)$data['sending_at'] : null;
        $this->deliveredAt = isset($data['delivered_at']) ? (int)$data['delivered_at'] : null;
        $this->inDate = isset($data['in_date']) ? (int)$data['in_date'] : null;
        $this->inDateHour = isset($data['in_date_hour']) ? (int)$data['in_date_hour'] : null;
        $this->inYearWeek = isset($data['in_year_week']) ? (int)$data['in_year_week'] : null;
        $this->inYearMonth = isset($data['in_year_month']) ? (int)$data['in_year_month'] : null;
        $this->inYear = isset($data['in_year']) ? (int)$data['in_year'] : null;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): LogEntry
    {
        $this->id = $id;
        return $this;
    }

    public function getUserGuid(): ?string
    {
        return $this->userGuid;
    }

    public function setUserGuid(?string $userGuid): LogEntry
    {
        $this->userGuid = $userGuid;
        return $this;
    }

    public function getUserDomainGuid(): ?string
    {
        return $this->userDomainGuid;
    }

    public function setUserDomainGuid(?string $userDomainGuid): LogEntry
    {
        $this->userDomainGuid = $userDomainGuid;
        return $this;
    }

    public function getUserSmtpGuid(): ?string
    {
        return $this->userSmtpGuid;
    }

    public function setUserSmtpGuid(?string $userSmtpGuid): LogEntry
    {
        $this->userSmtpGuid = $userSmtpGuid;
        return $this;
    }

    public function getWebhookGuid(): ?string
    {
        return $this->webhookGuid;
    }

    public function setWebhookGuid(?string $webhookGuid): LogEntry
    {
        $this->webhookGuid = $webhookGuid;
        return $this;
    }

    public function getMessageGuid(): ?string
    {
        return $this->messageGuid;
    }

    public function setMessageGuid(?string $messageGuid): LogEntry
    {
        $this->messageGuid = $messageGuid;
        return $this;
    }

    public function getServerMessageGuid(): ?string
    {
        return $this->serverMessageGuid;
    }

    public function setServerMessageGuid(?string $serverMessageGuid): LogEntry
    {
        $this->serverMessageGuid = $serverMessageGuid;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): LogEntry
    {
        $this->type = $type;
        return $this;
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(?string $sender): LogEntry
    {
        $this->sender = $sender;
        return $this;
    }

    public function getSenderDomain(): ?string
    {
        return $this->senderDomain;
    }

    public function setSenderDomain(?string $senderDomain): LogEntry
    {
        $this->senderDomain = $senderDomain;
        return $this;
    }

    public function getSenderIp(): ?string
    {
        return $this->senderIp;
    }

    public function setSenderIp(?string $senderIp): LogEntry
    {
        $this->senderIp = $senderIp;
        return $this;
    }

    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    public function setRecipient(?string $recipient): LogEntry
    {
        $this->recipient = $recipient;
        return $this;
    }

    public function getRecipientDomain(): ?string
    {
        return $this->recipientDomain;
    }

    public function setRecipientDomain(?string $recipientDomain): LogEntry
    {
        $this->recipientDomain = $recipientDomain;
        return $this;
    }

    public function getRecipientIp(): ?string
    {
        return $this->recipientIp;
    }

    public function setRecipientIp(?string $recipientIp): LogEntry
    {
        $this->recipientIp = $recipientIp;
        return $this;
    }

    public function getRecipientHash(): ?string
    {
        return $this->recipientHash;
    }

    public function setRecipientHash(?string $recipientHash): LogEntry
    {
        $this->recipientHash = $recipientHash;
        return $this;
    }

    public function getServer(): ?string
    {
        return $this->server;
    }

    public function setServer(?string $server): LogEntry
    {
        $this->server = $server;
        return $this;
    }

    public function getEventType(): ?string
    {
        return $this->eventType;
    }

    public function setEventType(?string $eventType): LogEntry
    {
        $this->eventType = $eventType;
        return $this;
    }

    public function getEvent(): ?string
    {
        return $this->event;
    }

    public function setEvent(?string $event): LogEntry
    {
        $this->event = $event;
        return $this;
    }

    public function getEventDetail(): ?string
    {
        return $this->eventDetail;
    }

    public function setEventDetail(?string $eventDetail): LogEntry
    {
        $this->eventDetail = $eventDetail;
        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): LogEntry
    {
        $this->tags = $tags;
        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): LogEntry
    {
        $this->subject = $subject;
        return $this;
    }

    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?int $createdAt): LogEntry
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getCreatedAtDateTime(): ?\DateTime
    {
        return $this->createdAt ? new \DateTime('@' . $this->createdAt) : null;
    }

    public function getSendingAt(): ?int
    {
        return $this->sendingAt;
    }

    public function setSendingAt(?int $sendingAt): LogEntry
    {
        $this->sendingAt = $sendingAt;
        return $this;
    }

    public function getSendingAtDateTime(): ?\DateTime
    {
        return $this->sendingAt ? new \DateTime('@' . $this->sendingAt) : null;
    }

    public function getDeliveredAt(): ?int
    {
        return $this->deliveredAt;
    }

    public function setDeliveredAt(?int $deliveredAt): LogEntry
    {
        $this->deliveredAt = $deliveredAt;
        return $this;
    }

    public function getDeliveredAtDateTime(): ?\DateTime
    {
        return $this->deliveredAt ? new \DateTime('@' . $this->deliveredAt) : null;
    }

    public function getInDate(): ?int
    {
        return $this->inDate;
    }

    public function setInDate(?int $inDate): LogEntry
    {
        $this->inDate = $inDate;
        return $this;
    }

    public function getInDateHour(): ?int
    {
        return $this->inDateHour;
    }

    public function setInDateHour(?int $inDateHour): LogEntry
    {
        $this->inDateHour = $inDateHour;
        return $this;
    }

    public function getInYearWeek(): ?int
    {
        return $this->inYearWeek;
    }

    public function setInYearWeek(?int $inYearWeek): LogEntry
    {
        $this->inYearWeek = $inYearWeek;
        return $this;
    }

    public function getInYearMonth(): ?int
    {
        return $this->inYearMonth;
    }

    public function setInYearMonth(?int $inYearMonth): LogEntry
    {
        $this->inYearMonth = $inYearMonth;
        return $this;
    }

    public function getInYear(): ?int
    {
        return $this->inYear;
    }

    public function setInYear(?int $inYear): LogEntry
    {
        $this->inYear = $inYear;
        return $this;
    }

    public function isQueued(): bool
    {
        return $this->eventType === self::SMTP_EVENT_QUEUED;
    }

    public function isSend(): bool
    {
        return $this->eventType === self::SMTP_EVENT_SEND;
    }

    public function isDelivered(): bool
    {
        return $this->eventType === self::SMTP_EVENT_DELIVERED;
    }

    public function isBounced(): bool
    {
        return $this->eventType === self::SMTP_EVENT_BOUNCED;
    }

    public function isFailed(): bool
    {
        return $this->eventType === self::SMTP_EVENT_FAILED;
    }

    public function isPermanentFail(): bool
    {
        return $this->eventType === self::SMTP_EVENT_PERMANENT_FAIL;
    }

    public function isOpened(): bool
    {
        return $this->eventType === self::SMTP_EVENT_OPENED;
    }

    public function isClicked(): bool
    {
        return $this->eventType === self::SMTP_EVENT_CLICKED;
    }

    public function isUnsubscribed(): bool
    {
        return $this->eventType === self::SMTP_EVENT_UNSUBSCRIBED;
    }

    public function isTempFailure(): bool
    {
        return $this->eventType === self::SMTP_EVENT_TEMP_FAILURE;
    }

    public function isDeferred(): bool
    {
        return $this->eventType === self::SMTP_EVENT_DEFERRED;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_guid' => $this->userGuid,
            'user_domain_guid' => $this->userDomainGuid,
            'user_smtp_guid' => $this->userSmtpGuid,
            'webhook_guid' => $this->webhookGuid,
            'message_guid' => $this->messageGuid,
            'server_message_guid' => $this->serverMessageGuid,
            'type' => $this->type,
            'sender' => $this->sender,
            'sender_domain' => $this->senderDomain,
            'sender_ip' => $this->senderIp,
            'recipient' => $this->recipient,
            'recipient_domain' => $this->recipientDomain,
            'recipient_ip' => $this->recipientIp,
            'recipient_hash' => $this->recipientHash,
            'server' => $this->server,
            'event_type' => $this->eventType,
            'event' => $this->event,
            'event_detail' => $this->eventDetail,
            'tags' => $this->tags,
            'subject' => $this->subject,
            'created_at' => $this->createdAt,
            'sending_at' => $this->sendingAt,
            'delivered_at' => $this->deliveredAt,
            'in_date' => $this->inDate,
            'in_date_hour' => $this->inDateHour,
            'in_year_week' => $this->inYearWeek,
            'in_year_month' => $this->inYearMonth,
            'in_year' => $this->inYear,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}