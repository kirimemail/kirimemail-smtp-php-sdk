<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Model;

use JsonSerializable;

class Suppression implements JsonSerializable
{
    private ?int $id;
    private ?string $userGuid;
    private ?string $userDomainGuid;
    private ?string $type;
    private ?string $recipientType;
    private ?string $recipient;
    private ?string $description;
    private ?string $source;
    private ?array $tags;
    private ?int $createdAt;
    private ?int $modifiedAt;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->userGuid = $data['user_guid'] ?? null;
        $this->userDomainGuid = $data['user_domain_guid'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->recipientType = $data['recipient_type'] ?? null;
        $this->recipient = $data['recipient'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->source = $data['source'] ?? null;
        $this->tags = $data['tags'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->modifiedAt = $data['modified_at'] ?? null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Suppression
    {
        $this->id = $id;
        return $this;
    }

    public function getUserGuid(): ?string
    {
        return $this->userGuid;
    }

    public function setUserGuid(?string $userGuid): Suppression
    {
        $this->userGuid = $userGuid;
        return $this;
    }

    public function getUserDomainGuid(): ?string
    {
        return $this->userDomainGuid;
    }

    public function setUserDomainGuid(?string $userDomainGuid): Suppression
    {
        $this->userDomainGuid = $userDomainGuid;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): Suppression
    {
        $this->type = $type;
        return $this;
    }

    public function getRecipientType(): ?string
    {
        return $this->recipientType;
    }

    public function setRecipientType(?string $recipientType): Suppression
    {
        $this->recipientType = $recipientType;
        return $this;
    }

    public function getRecipient(): ?string
    {
        return $this->recipient;
    }

    public function setRecipient(?string $recipient): Suppression
    {
        $this->recipient = $recipient;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Suppression
    {
        $this->description = $description;
        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): Suppression
    {
        $this->source = $source;
        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): Suppression
    {
        $this->tags = $tags;
        return $this;
    }

    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?int $createdAt): Suppression
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getModifiedAt(): ?int
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(?int $modifiedAt): Suppression
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

    public function isUnsubscribe(): bool
    {
        return $this->type === 'unsubscribe';
    }

    public function isBounce(): bool
    {
        return $this->type === 'bounce';
    }

    public function isWhitelist(): bool
    {
        return $this->type === 'whitelist';
    }

    public function isEmailType(): bool
    {
        return $this->recipientType === 'email';
    }

    public function isDomainType(): bool
    {
        return $this->recipientType === 'domain';
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_guid' => $this->userGuid,
            'user_domain_guid' => $this->userDomainGuid,
            'type' => $this->type,
            'recipient_type' => $this->recipientType,
            'recipient' => $this->recipient,
            'description' => $this->description,
            'source' => $this->source,
            'tags' => $this->tags,
            'created_at' => $this->createdAt,
            'modified_at' => $this->modifiedAt,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}