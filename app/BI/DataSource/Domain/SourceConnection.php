<?php

namespace App\BI\DataSource\Domain;

final class SourceConnection
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $label,
        public readonly string  $host,
        public readonly int     $port,
        public readonly string  $databaseName,
        public readonly string  $username,
        public readonly string  $password,
        public readonly string  $driver,
        public readonly bool    $isActive,
        public readonly ?string $lastTestedAt = null,
    ) {}

    public function isMySQL(): bool
    {
        return $this->driver === 'mysql';
    }

    public function isPostgres(): bool
    {
        return $this->driver === 'pgsql';
    }

    public function withLastTestedAt(string $testedAt): self
    {
        return new self(
            id:           $this->id,
            label:        $this->label,
            host:         $this->host,
            port:         $this->port,
            databaseName: $this->databaseName,
            username:     $this->username,
            password:     $this->password,
            driver:       $this->driver,
            isActive:     $this->isActive,
            lastTestedAt: $testedAt,
        );
    }
}
