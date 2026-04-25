<?php

namespace App\BI\Analysis\Domain\Indicator;

final class AverageBehaviorByActivityIndicator implements IndicatorInterface
{
    public function key(): string
    {
        return 'avg_behavior_by_activity';
    }

    public function label(): string
    {
        return 'Comportement moyen par activité';
    }

    public function target(): IndicatorTargetEnum
    {
        return IndicatorTargetEnum::Transmissions;
    }

    public function outputType(): IndicatorOutputTypeEnum
    {
        return IndicatorOutputTypeEnum::GroupedAverage;
    }

    public function parametersSchema(): ?array
    {
        return null;
    }

    public function compute(array $dataset, array $parameters = []): mixed
    {
        return collect($dataset)
            ->groupBy('activitySlug')
            ->map(fn ($group) => round($group->avg('behavior'), 2))
            ->all();
    }
}
