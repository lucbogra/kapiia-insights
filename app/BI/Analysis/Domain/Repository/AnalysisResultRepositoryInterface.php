<?php

namespace App\BI\Analysis\Domain\Repository;

use App\BI\Analysis\Domain\AnalysisResult;

interface AnalysisResultRepositoryInterface
{
    public function findFreshByArchetype(string $archetypeId): ?AnalysisResult;

    public function save(AnalysisResult $result): void;

    public function delete(string $id): void;

    public function deleteExpired(): void;

}