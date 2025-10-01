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

    private ?int $id;
    private ?string $userGuid;
    private ?string $userDomainGuid;
    private ?string $eventType;
    private ?string $messageGuid;
    private ?int $timestamp;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->userGuid = $data['user_guid'] ?? null;
        $this->userDomainGuid = $data['user_domain_guid'] ?? null;
        $this->eventType = $data['event_type'] ?? null;
        $this->messageGuid = $data['message_guid'] ?? null;
        $this->timestamp = $data['timestamp'] ?? null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): LogEntry
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

    public function getEventType(): ?string
    {
        return $this->eventType;
    }

    public function setEventType(?string $eventType): LogEntry
    {
        $this->eventType = $eventType;
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

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setTimestamp(?int $timestamp): LogEntry
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function getTimestampDateTime(): ?\DateTime
    {
        return $this->timestamp ? new \DateTime('@' . $this->timestamp) : null;
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
            'event_type' => $this->eventType,
            'message_guid' => $this->messageGuid,
            'timestamp' => $this->timestamp,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}