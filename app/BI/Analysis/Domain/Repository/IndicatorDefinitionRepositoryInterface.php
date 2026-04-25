<?php

namespace App\BI\Analysis\Domain\Repository;

use App\BI\Analysis\Domain\IndicatorDefinition;

interface IndicatorDefinitionRepositoryInterface
{
    public function findById(string $id): ?IndicatorDefinition;

    public function findByKey(string $key): ?IndicatorDefinition;

    /** @return IndicatorDefinition[] */
    public function findAllActive(): array;

    /** @return IndicatorDefinition[] */
    public function findAll(): array;

    public function save(IndicatorDefinition $definition): void;

    public function delete(string $id): void;
}
