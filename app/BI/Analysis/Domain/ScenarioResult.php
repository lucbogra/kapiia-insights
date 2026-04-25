<?php

namespace App\BI\Analysis\Domain;

final class ScenarioResult
{
    /**
     * @param  array<string, mixed>  $indicatorResults  Clé = indicator key, valeur = résultat du compute()
     * @param  string[]  $sourceLabels
     */
    public function __construct(
        public readonly string $scenarioId,
        public readonly int $populationCount,
        public readonly int $incidentCount,
        public readonly array $indicatorResults,
        public readonly array $sourceLabels,
        public readonly string $computedAt,
    ) {}
}
