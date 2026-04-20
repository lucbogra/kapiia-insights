<?php

namespace App\BI\Analysis\Domain;

final class AnalysisResult
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $archetypeId,
        public readonly array   $sourceConnectionIds,
        public readonly array   $payload,
        public readonly int     $populationCount,
        public readonly ?string $expiresAt,
    ) {}

    public function isExpired(): bool
    {
        if ($this->expiresAt === null) {
            return false;
        }

        return now()->isAfter($this->expiresAt);
    }

    public function isFresh(): bool
    {
        return ! $this->isExpired();
    }
}
