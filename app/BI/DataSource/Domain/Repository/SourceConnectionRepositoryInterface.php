<?php

namespace App\BI\DataSource\Domain\Repository;

use App\BI\DataSource\Domain\SourceConnection;

interface SourceConnectionRepositoryInterface
{
    public function findById(string $id): ?SourceConnection;

    /** @return SourceConnection[] */
    public function findAllActive(): array;

    /** @return SourceConnection[] */
    public function findAll(): array;

    public function save(SourceConnection $sourceConnection): void;

    public function delete(string $id): void;
}
