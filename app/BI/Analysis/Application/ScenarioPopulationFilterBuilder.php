<?php

namespace App\BI\Analysis\Application;

use App\BI\Analysis\Domain\ArchetypePopulationFilter;
use App\BI\Analysis\Domain\Scenario;
use App\BI\Profiling\Domain\Criteria\DiscreteCriterion;
use App\BI\Profiling\Domain\Criteria\RangeCriterion;
use App\BI\Profiling\Domain\Repository\ArchetypeCriterionRepositoryInterface;
use App\BI\Profiling\Domain\Repository\ArchetypeRepositoryInterface;

final class ScenarioPopulationFilterBuilder
{
    public function __construct(
        private readonly ArchetypeRepositoryInterface $archetypeRepository,
        private readonly ArchetypeCriterionRepositoryInterface $criterionRepository,
    ) {}

    public function build(Scenario $scenario): ArchetypePopulationFilter
    {
        $criteriaValues = $this->resolveCriteriaValues($scenario);
        $criteria = $this->criterionRepository->findAll();

        $resolved = [];

        // dd($criteriaValues);

        foreach ($criteria as $criterion) {
            $value = $criteriaValues[$criterion->key] ?? null;

            if ($value === null) {
                continue;
            }

            $resolved[] = $criterion->isRange()
                ? new RangeCriterion(
                    column: $criterion->sourceColumn,
                    min: $value['from'] ?? $value['min'] ?? null,
                    max: $value['to'] ?? $value['max'] ?? null,
                )
                : new DiscreteCriterion(
                    column: $criterion->sourceColumn,
                    value: $value,
                );
        }

        return new ArchetypePopulationFilter($resolved);
    }

    private function resolveCriteriaValues(Scenario $scenario): array
    {
        if ($scenario->usesArchetype()) {
            $archetype = $this->archetypeRepository->findById($scenario->archetypeId);

            if (! $archetype) {
                throw new \DomainException(
                    "L'archétype référencé est introuvable : {$scenario->archetypeId}"
                );
            }

            return $archetype->criteriaValues;
        }

        return $scenario->criteriaValues ?? [];
    }
}
