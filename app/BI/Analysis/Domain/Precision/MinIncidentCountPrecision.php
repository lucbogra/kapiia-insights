<?php

namespace App\BI\Analysis\Domain\Precision;

use Illuminate\Database\Query\Builder;

final class MinIncidentCountPrecision implements PrecisionInterface
{
    public function key(): string
    {
        return 'min_incident_count';
    }

    public function label(): string
    {
        return 'Usagers ayant un nombre minimum d\'incidents';
    }

    public function type(): PrecisionTypeEnum
    {
        return PrecisionTypeEnum::PopulationFilter;
    }

    public function target(): PrecisionTargetEnum
    {
        return PrecisionTargetEnum::Incidents;
    }

    public function parametersSchema(): ?array
    {
        return [
            'min_count' => [
                'type' => 'int',
                'label' => 'Nombre minimum d\'incidents',
                'default' => 10,
                'min' => 1,
                'max' => 1000,
            ],
        ];
    }

    /**
     * Applique une sous-requête : usager_id IN (
     *   SELECT usager_id FROM incidents GROUP BY usager_id HAVING COUNT(*) >= N
     * )
     */
    public function apply(Builder $query, array $parameters): void
    {
        $minCount = (int) ($parameters['min_count'] ?? 10);

        $query->whereIn('usagers_view.id', function ($sub) use ($minCount) {
            $sub->from('incidents')
                ->select('usager_id')
                ->groupBy('usager_id')
                ->havingRaw('COUNT(*) >= ?', [$minCount]);
        });
    }
}
