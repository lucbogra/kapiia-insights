<?php

namespace App\BI\Analysis\Domain\Precision;

use Illuminate\Database\Query\Builder;

final class MinGraviteThresholdPrecision implements PrecisionInterface
{
    public function key(): string
    {
        return 'min_gravite_threshold';
    }

    public function label(): string
    {
        return 'Usagers ayant des incidents au-dessus d\'un seuil de gravité';
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
            'min_gravite' => [
                'type' => 'int',
                'label' => 'Gravité minimale',
                'default' => 3,
                'min' => 1,
                'max' => 5,
            ],
            'min_count' => [
                'type' => 'int',
                'label' => 'Nombre minimum d\'occurrences',
                'default' => 1,
                'min' => 1,
            ],
        ];
    }

    public function apply(Builder $query, array $parameters): void
    {
        $minGravite = (int) ($parameters['min_gravite'] ?? 3);
        $minCount = (int) ($parameters['min_count'] ?? 1);

        $query->whereIn('usagers_view.id', function ($sub) use ($minGravite, $minCount) {
            $sub->from('incidents')
                ->select('usager_id')
                ->where('gravite', '>=', $minGravite)
                ->groupBy('usager_id')
                ->havingRaw('COUNT(*) >= ?', [$minCount]);
        });
    }
}
