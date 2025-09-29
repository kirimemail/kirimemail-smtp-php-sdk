<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Model;

use JsonSerializable;

class Credential implements JsonSerializable
{
    private ?int $id;
    private ?string $userSmtpGuid;
    private ?string $username;
    private ?bool $isVerified;
    private ?bool $status;
    private ?bool $isDeleted;
    private ?int $createdAt;
    private ?int $modifiedAt;
    private ?int $deletedAt;
    private ?int $lastPasswordChanged;
    private ?string $password;
    private ?array $strengthInfo;
    private ?bool $remoteSynced;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->userSmtpGuid = $data['user_smtp_guid'] ?? null;
        $this->username = $data['username'] ?? null;
        $this->isVerified = $data['is_verified'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->isDeleted = $data['is_deleted'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->modifiedAt = $data['modified_at'] ?? null;
        $this->deletedAt = $data['deleted_at'] ?? null;
        $this->lastPasswordChanged = $data['last_password_changed'] ?? null;
        $this->password = $data['password'] ?? null;
        $this->strengthInfo = $data['strength_info'] ?? null;
        $this->remoteSynced = $data['remote_synced'] ?? null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Credential
    {
        $this->id = $id;
        return $this;
    }

    public function getUserSmtpGuid(): ?string
    {
        return $this->userSmtpGuid;
    }

    public function setUserSmtpGuid(?string $userSmtpGuid): Credential
    {
        $this->userSmtpGuid = $userSmtpGuid;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): Credential
    {
        $this->username = $username;
        return $this;
    }

    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?int $createdAt): Credential
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getModifiedAt(): ?int
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(?int $modifiedAt): Credential
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

    public function getDeletedAtDateTime(): ?\DateTime
    {
        return $this->deletedAt ? new \DateTime('@' . $this->deletedAt) : null;
    }

    public function getLastPasswordChangedDateTime(): ?\DateTime
    {
        return $this->lastPasswordChanged ? new \DateTime('@' . $this->lastPasswordChanged) : null;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(?bool $isVerified): Credential
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function setVerified(?bool $verified): Credential
    {
        $this->isVerified = $verified;
        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): Credential
    {
        $this->status = $status;
        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(?bool $isDeleted): Credential
    {
        $this->isDeleted = $isDeleted;
        return $this;
    }

    public function getDeletedAt(): ?int
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?int $deletedAt): Credential
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    public function getLastPasswordChanged(): ?int
    {
        return $this->lastPasswordChanged;
    }

    public function setLastPasswordChanged(?int $lastPasswordChanged): Credential
    {
        $this->lastPasswordChanged = $lastPasswordChanged;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): Credential
    {
        $this->password = $password;
        return $this;
    }

    public function getStrengthInfo(): ?array
    {
        return $this->strengthInfo;
    }

    public function setStrengthInfo(?array $strengthInfo): Credential
    {
        $this->strengthInfo = $strengthInfo;
        return $this;
    }

    public function isRemoteSynced(): ?bool
    {
        return $this->remoteSynced;
    }

    public function setRemoteSynced(?bool $remoteSynced): Credential
    {
        $this->remoteSynced = $remoteSynced;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_smtp_guid' => $this->userSmtpGuid,
            'username' => $this->username,
            'is_verified' => $this->isVerified,
            'status' => $this->status,
            'is_deleted' => $this->isDeleted,
            'created_at' => $this->createdAt,
            'modified_at' => $this->modifiedAt,
            'deleted_at' => $this->deletedAt,
            'last_password_changed' => $this->lastPasswordChanged,
            'password' => $this->password,
            'strength_info' => $this->strengthInfo,
            'remote_synced' => $this->remoteSynced,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}