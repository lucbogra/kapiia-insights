<?php

namespace App\BI\Analysis\Domain\Indicator;

final class TopIncidentTitlesIndicator implements IndicatorInterface
{
    public function key(): string
    {
        return 'top_incident_titles';
    }

    public function label(): string
    {
        return 'Incidents les plus fréquents';
    }

    public function target(): IndicatorTargetEnum
    {
        return IndicatorTargetEnum::Incidents;
    }

    public function outputType(): IndicatorOutputTypeEnum
    {
        return IndicatorOutputTypeEnum::ListItems;
    }

    public function parametersSchema(): ?array
    {
        return [
            'limit' => ['type' => 'int', 'default' => 10, 'min' => 1, 'max' => 50],
        ];
    }

    public function compute(array $dataset, array $parameters = []): mixed
    {
        $limit = (int) ($parameters['limit'] ?? 10);

        return collect($dataset)
            ->groupBy('intitule')
            ->map->count()
            ->sortDesc()
            ->take($limit)
            ->all();
    }
}
