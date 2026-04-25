<?php

namespace App\BI\Analysis\Domain\Precision;

use Illuminate\Database\Query\Builder;

final class IncidentDateRangePrecision implements PrecisionInterface
{
    public function key(): string
    {
        return 'incident_date_range';
    }

    public function label(): string
    {
        return 'Incidents dans une plage de dates';
    }

    public function type(): PrecisionTypeEnum
    {
        return PrecisionTypeEnum::DatasetFilter;
    }

    public function target(): PrecisionTargetEnum
    {
        return PrecisionTargetEnum::Incidents;
    }

    public function parametersSchema(): ?array
    {
        return [
            'date_from' => ['type' => 'date', 'label' => 'Date de début'],
            'date_to' => ['type' => 'date', 'label' => 'Date de fin'],
        ];
    }

    public function apply(Builder $query, array $parameters): void
    {
        if (! empty($parameters['date_from'])) {
            $query->where('incidents.date_incident', '>=', $parameters['date_from']);
        }

        if (! empty($parameters['date_to'])) {
            $query->where('incidents.date_incident', '<=', $parameters['date_to']);
        }
    }
}
