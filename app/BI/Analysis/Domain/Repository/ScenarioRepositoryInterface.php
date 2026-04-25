<?php

namespace App\BI\Analysis\Domain\Repository;

use App\BI\Analysis\Domain\Scenario;

interface ScenarioRepositoryInterface
{
    public function findById(string $id): ?Scenario;

    /** @return Scenario[] */
    public function findVisibleTo(string $userId): array;

    /** @return Scenario[] */
    public function findOwnedBy(string $userId): array;

    public function save(Scenario $scenario): void;

    public function delete(string $id): void;
}
