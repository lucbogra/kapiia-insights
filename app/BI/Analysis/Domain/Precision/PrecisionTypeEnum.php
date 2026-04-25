<?php

namespace App\BI\Analysis\Domain\Precision;

enum PrecisionTypeEnum: string
{
    case PopulationFilter = 'population_filter';  // filtre sur quels usagers
    case DatasetFilter = 'dataset_filter';     // filtre sur quelles données

    public function label(): string
    {
        return match ($this) {
            self::PopulationFilter => 'Filtre de population',
            self::DatasetFilter => 'Filtre de données',
        };
    }
}
