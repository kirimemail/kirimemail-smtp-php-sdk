<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Model;

use JsonSerializable;

class EmailValidationResult implements JsonSerializable
{
    private ?string $email;
    private ?bool $isValid;
    private ?string $error;
    private ?array $warnings;
    private ?bool $cached;
    private ?string $validatedAt;
    private ?bool $isSpamtrap;
    private ?float $spamtrapScore;

    public function __construct(array $data = [])
    {
        $this->email = $data['email'] ?? null;
        $this->isValid = $data['is_valid'] ?? null;
        $this->error = $data['error'] ?? null;
        $this->warnings = $data['warnings'] ?? [];
        $this->cached = $data['cached'] ?? null;
        $this->validatedAt = $data['validated_at'] ?? null;
        $this->isSpamtrap = $data['is_spamtrap'] ?? null;
        $this->spamtrapScore = $data['spamtrap_score'] ?? null;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): EmailValidationResult
    {
        $this->email = $email;
        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(?bool $isValid): EmailValidationResult
    {
        $this->isValid = $isValid;
        return $this;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): EmailValidationResult
    {
        $this->error = $error;
        return $this;
    }

    public function getWarnings(): ?array
    {
        return $this->warnings;
    }

    public function setWarnings(?array $warnings): EmailValidationResult
    {
        $this->warnings = $warnings;
        return $this;
    }

    public function isCached(): ?bool
    {
        return $this->cached;
    }

    public function setCached(?bool $cached): EmailValidationResult
    {
        $this->cached = $cached;
        return $this;
    }

    public function getValidatedAt(): ?string
    {
        return $this->validatedAt;
    }

    public function setValidatedAt(?string $validatedAt): EmailValidationResult
    {
        $this->validatedAt = $validatedAt;
        return $this;
    }

    public function getValidatedAtDateTime(): ?\DateTime
    {
        return $this->validatedAt ? new \DateTime($this->validatedAt) : null;
    }

    public function isSpamtrap(): ?bool
    {
        return $this->isSpamtrap;
    }

    public function setIsSpamtrap(?bool $isSpamtrap): EmailValidationResult
    {
        $this->isSpamtrap = $isSpamtrap;
        return $this;
    }

    public function getSpamtrapScore(): ?float
    {
        return $this->spamtrapScore;
    }

    public function setSpamtrapScore(?float $spamtrapScore): EmailValidationResult
    {
        $this->spamtrapScore = $spamtrapScore;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'is_valid' => $this->isValid,
            'error' => $this->error,
            'warnings' => $this->warnings,
            'cached' => $this->cached,
            'validated_at' => $this->validatedAt,
            'is_spamtrap' => $this->isSpamtrap,
            'spamtrap_score' => $this->spamtrapScore,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
