<?php

namespace App\BI\Profiling\Domain\Repository;

use App\BI\Profiling\Domain\Archetype;

interface ArchetypeRepositoryInterface
{
    public function findById(string $id): ?Archetype;

    public function findByHash(string $hash): ?Archetype;

    /** @return Archetype[] */
    public function findAllActive(): array;

    public function save(Archetype $archetype): void;

    public function delete(string $id): void;
}
