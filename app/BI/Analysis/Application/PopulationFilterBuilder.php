<?php

namespace App\BI\Analysis\Application;

use App\BI\Analysis\Domain\ArchetypePopulationFilter;
use App\BI\Profiling\Domain\ArchetypeCriterion;
use App\BI\Profiling\Domain\Criteria\DiscreteCriterion;
use App\BI\Profiling\Domain\Criteria\RangeCriterion;

final class PopulationFilterBuilder
{
    /**
     * @param  array  $criteriaValues  Valeurs stockées dans l'archétype
     * @param  ArchetypeCriterion[]  $criteria  Définitions des critères
     */
    public function build(array $criteriaValues, array $criteria): ArchetypePopulationFilter
    {
        $resolved = [];

        foreach ($criteria as $criterion) {
            $value = $criteriaValues[$criterion->key] ?? null;

            if ($value === null) {
                continue;
            }

            if ($criterion->isRange()) {
                $resolved[] = new RangeCriterion(
                    column: $criterion->sourceColumn,
                    min: $value['from'] ?? $value['min'] ?? null,
                    max: $value['to'] ?? $value['max'] ?? null,
                );
            } else {
                $resolved[] = new DiscreteCriterion(
                    column: $criterion->sourceColumn,
                    value: $value,
                );
            }
        }

        return new ArchetypePopulationFilter($resolved);
    }
}
