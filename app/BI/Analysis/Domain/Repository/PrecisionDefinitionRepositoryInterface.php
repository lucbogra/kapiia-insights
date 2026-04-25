<?php

namespace App\BI\Analysis\Domain\Repository;

use App\BI\Analysis\Domain\PrecisionDefinition;

interface PrecisionDefinitionRepositoryInterface
{
    public function findById(string $id): ?PrecisionDefinition;

    public function findByKey(string $key): ?PrecisionDefinition;

    /** @return PrecisionDefinition[] */
    public function findAllActive(): array;

    /** @return PrecisionDefinition[] */
    public function findAll(): array;

    public function save(PrecisionDefinition $definition): void;

    public function delete(string $id): void;
}
