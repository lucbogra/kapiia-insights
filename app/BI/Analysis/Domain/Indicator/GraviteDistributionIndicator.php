<?php

namespace App\BI\Analysis\Domain\Indicator;

final class GraviteDistributionIndicator implements IndicatorInterface
{
    public function key(): string
    {
        return 'gravite_distribution';
    }

    public function label(): string
    {
        return 'Distribution des gravités';
    }

    public function target(): IndicatorTargetEnum
    {
        return IndicatorTargetEnum::Incidents;
    }

    public function outputType(): IndicatorOutputTypeEnum
    {
        return IndicatorOutputTypeEnum::Distribution;
    }

    public function parametersSchema(): ?array
    {
        return null;
    }

    public function compute(array $dataset, array $parameters = []): mixed
    {
        return collect($dataset)
            ->groupBy('gravite')
            ->map->count()
            ->sortKeys()
            ->all();
    }
}
