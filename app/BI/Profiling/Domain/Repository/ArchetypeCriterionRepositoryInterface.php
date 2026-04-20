<?php

namespace App\BI\Profiling\Domain\Repository;

use App\BI\Profiling\Domain\ArchetypeCriterion;

interface ArchetypeCriterionRepositoryInterface
{
    public function findById(string $id): ?ArchetypeCriterion;

    /** @return ArchetypeCriterion[] */
    public function findAll(): array;

    public function save(ArchetypeCriterion $criterion): void;

    public function delete(string $id): void;
}
