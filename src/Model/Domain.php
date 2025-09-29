<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Model;

use JsonSerializable;

class Domain implements JsonSerializable
{
    private ?int $id;
    private ?string $domain;
    private ?string $tracklinkDomain;
    private ?bool $tracklinkDomainIsVerified;
    private ?bool $authDomainIsVerified;
    private ?string $dnsSelector;
    private ?string $dnsRecord;
    private ?bool $clickTrack;
    private ?bool $openTrack;
    private ?bool $unsubTrack;
    private ?bool $isVerified;
    private ?bool $status;
    private ?int $createdAt;
    private ?int $modifiedAt;
    private ?string $authDomain;
    private ?string $authDomainDkimRecord;
    private ?string $authDomainDkimSelector;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->domain = $data['domain'] ?? null;
        $this->tracklinkDomain = $data['tracklink_domain'] ?? null;
        $this->tracklinkDomainIsVerified = $data['tracklink_domain_is_verified'] ?? null;
        $this->authDomainIsVerified = $data['auth_domain_is_verified'] ?? null;
        $this->dnsSelector = $data['dns_selector'] ?? null;
        $this->dnsRecord = $data['dns_record'] ?? null;
        $this->clickTrack = $data['click_track'] ?? null;
        $this->openTrack = $data['open_track'] ?? null;
        $this->unsubTrack = $data['unsub_track'] ?? null;
        $this->isVerified = $data['is_verified'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->createdAt = $data['created_at'] ?? null;
        $this->modifiedAt = $data['modified_at'] ?? null;
        $this->authDomain = $data['auth_domain'] ?? null;
        $this->authDomainDkimRecord = $data['auth_domain_dkim_record'] ?? null;
        $this->authDomainDkimSelector = $data['auth_domain_dkim_selector'] ?? null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Domain
    {
        $this->id = $id;
        return $this;
    }

    public function getDomain(): ?string
    {
        return $this->domain;
    }

    public function setDomain(?string $domain): Domain
    {
        $this->domain = $domain;
        return $this;
    }

    public function isVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(?bool $isVerified): Domain
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getAuthDomain(): ?string
    {
        return $this->authDomain;
    }

    public function setAuthDomain(?string $authDomain): Domain
    {
        $this->authDomain = $authDomain;
        return $this;
    }

    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?int $createdAt): Domain
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getModifiedAt(): ?int
    {
        return $this->modifiedAt;
    }

    public function setModifiedAt(?int $modifiedAt): Domain
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

    public function getTracklinkDomain(): ?string
    {
        return $this->tracklinkDomain;
    }

    public function setTracklinkDomain(?string $tracklinkDomain): Domain
    {
        $this->tracklinkDomain = $tracklinkDomain;
        return $this;
    }

    public function isTracklinkDomainVerified(): ?bool
    {
        return $this->tracklinkDomainIsVerified;
    }

    public function setTracklinkDomainVerified(?bool $tracklinkDomainIsVerified): Domain
    {
        $this->tracklinkDomainIsVerified = $tracklinkDomainIsVerified;
        return $this;
    }

    public function isAuthDomainVerified(): ?bool
    {
        return $this->authDomainIsVerified;
    }

    public function setAuthDomainVerified(?bool $authDomainIsVerified): Domain
    {
        $this->authDomainIsVerified = $authDomainIsVerified;
        return $this;
    }

    public function getDnsSelector(): ?string
    {
        return $this->dnsSelector;
    }

    public function setDnsSelector(?string $dnsSelector): Domain
    {
        $this->dnsSelector = $dnsSelector;
        return $this;
    }

    public function getDnsRecord(): ?string
    {
        return $this->dnsRecord;
    }

    public function setDnsRecord(?string $dnsRecord): Domain
    {
        $this->dnsRecord = $dnsRecord;
        return $this;
    }

    public function hasClickTrack(): ?bool
    {
        return $this->clickTrack;
    }

    public function setClickTrack(?bool $clickTrack): Domain
    {
        $this->clickTrack = $clickTrack;
        return $this;
    }

    public function hasOpenTrack(): ?bool
    {
        return $this->openTrack;
    }

    public function setOpenTrack(?bool $openTrack): Domain
    {
        $this->openTrack = $openTrack;
        return $this;
    }

    public function hasUnsubTrack(): ?bool
    {
        return $this->unsubTrack;
    }

    public function setUnsubTrack(?bool $unsubTrack): Domain
    {
        $this->unsubTrack = $unsubTrack;
        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): Domain
    {
        $this->status = $status;
        return $this;
    }

    public function getAuthDomainDkimRecord(): ?string
    {
        return $this->authDomainDkimRecord;
    }

    public function setAuthDomainDkimRecord(?string $authDomainDkimRecord): Domain
    {
        $this->authDomainDkimRecord = $authDomainDkimRecord;
        return $this;
    }

    public function getAuthDomainDkimSelector(): ?string
    {
        return $this->authDomainDkimSelector;
    }

    public function setAuthDomainDkimSelector(?string $authDomainDkimSelector): Domain
    {
        $this->authDomainDkimSelector = $authDomainDkimSelector;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'domain' => $this->domain,
            'tracklink_domain' => $this->tracklinkDomain,
            'tracklink_domain_is_verified' => $this->tracklinkDomainIsVerified,
            'auth_domain_is_verified' => $this->authDomainIsVerified,
            'dns_selector' => $this->dnsSelector,
            'dns_record' => $this->dnsRecord,
            'click_track' => $this->clickTrack,
            'open_track' => $this->openTrack,
            'unsub_track' => $this->unsubTrack,
            'is_verified' => $this->isVerified,
            'status' => $this->status,
            'created_at' => $this->createdAt,
            'modified_at' => $this->modifiedAt,
            'auth_domain' => $this->authDomain,
            'auth_domain_dkim_record' => $this->authDomainDkimRecord,
            'auth_domain_dkim_selector' => $this->authDomainDkimSelector,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}