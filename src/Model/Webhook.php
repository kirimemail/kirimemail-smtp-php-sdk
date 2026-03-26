<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Model;

use JsonSerializable;

class Webhook implements JsonSerializable
{
    private ?string $webhookGuid;
    private ?string $userGuid;
    private ?string $userDomainGuid;
    private ?string $userSmtpGuid;
    private ?string $type;
    private ?string $url;
    private ?bool $isDeleted;
    private ?int $createdAt;
    private ?int $modifiedAt;

    public function __construct(array $data = [])
    {
        $this->webhookGuid = $data['webhook_guid'] ?? null;
        $this->userGuid = $data['user_guid'] ?? null;
        $this->userDomainGuid = $data['user_domain_guid'] ?? null;
        $this->userSmtpGuid = $data['user_smtp_guid'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->url = $data['url'] ?? null;
        $this->isDeleted = $data['is_deleted'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->modifiedAt = $data['modified_at'] ?? null;
    }

    public function getWebhookGuid(): ?string
    {
        return $this->webhookGuid;
    }

    public function setWebhookGuid(?string $webhookGuid): Webhook
    {
        $this->webhookGuid = $webhookGuid;
        return $this;
    }

    public function getUserGuid(): ?string
    {
        return $this->userGuid;
    }

    public function setUserGuid(?string $userGuid): Webhook
    {
        $this->userGuid = $userGuid;
        return $this;
    }

    public function getUserDomainGuid(): ?string
    {
        return $this->userDomainGuid;
    }

    public function setUserDomainGuid(?string $userDomainGuid): Webhook
    {
        $this->userDomainGuid = $userDomainGuid;
        return $this;
    }

    public function getUserSmtpGuid(): ?string
    {
        return $this->userSmtpGuid;
    }

    public function setUserSmtpGuid(?string $userSmtpGuid): Webhook
    {
        $this->userSmtpGuid = $userSmtpGuid;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): Webhook
    {
        $this->type = $type;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): Webhook
    {
        $this->url = $url;
        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setDeleted(?bool $isDeleted): Webhook
    {
        $this->isDeleted = $isDeleted;
        return $this;
    }

    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?int $createdAt): Webhook
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getModifiedAt(): ?int
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(?int $modifiedAt): Webhook
    {
        $this->modifiedAt = $modifiedAt;
        return $this;
    }

    public function getCreatedAtDateTime(): ?\DateTime
    {
        return $this->createdAt ? new \DateTime('@' . $this->createdAt) : null;
    }

    public function getModifiedAtDateTime(): ?\DateTime
    {
        return $this->modifiedAt ? new \DateTime('@' . $this->modifiedAt) : null;
    }

    public function toArray(): array
    {
        return [
            'webhook_guid' => $this->webhookGuid,
            'user_guid' => $this->userGuid,
            'user_domain_guid' => $this->userDomainGuid,
            'user_smtp_guid' => $this->userSmtpGuid,
            'type' => $this->type,
            'url' => $this->url,
            'is_deleted' => $this->isDeleted,
            'created_at' => $this->createdAt,
            'modified_at' => $this->modifiedAt,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
