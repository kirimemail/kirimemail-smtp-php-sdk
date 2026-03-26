<?php

declare(strict_types=1);

namespace KirimEmail\Smtp\Model;

use JsonSerializable;

class EmailValidationBatchResult implements JsonSerializable
{
    private ?array $results;
    private ?array $summary;

    public function __construct(array $data = [])
    {
        $this->results = [];
        foreach ($data['results'] ?? [] as $resultData) {
            $this->results[] = new EmailValidationResult($resultData);
        }

        $this->summary = $data['summary'] ?? null;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function setResults(array $results): EmailValidationBatchResult
    {
        $this->results = $results;
        return $this;
    }

    public function getSummary(): ?array
    {
        return $this->summary;
    }

    public function setSummary(?array $summary): EmailValidationBatchResult
    {
        $this->summary = $summary;
        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->summary['total'] ?? null;
    }

    public function getValidCount(): ?int
    {
        return $this->summary['valid'] ?? null;
    }

    public function getInvalidCount(): ?int
    {
        return $this->summary['invalid'] ?? null;
    }

    public function getCachedCount(): ?int
    {
        return $this->summary['cached'] ?? null;
    }

    public function getValidatedCount(): ?int
    {
        return $this->summary['validated'] ?? null;
    }

    public function toArray(): array
    {
        $resultsArray = [];
        foreach ($this->results as $result) {
            $resultsArray[] = $result->toArray();
        }

        return [
            'results' => $resultsArray,
            'summary' => $this->summary,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
