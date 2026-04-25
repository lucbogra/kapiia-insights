<?php

namespace App\BI\Analysis\Domain\Indicator;

final class AverageGraviteIndicator implements IndicatorInterface
{
    public function key(): string
    {
        return 'average_gravite';
    }

    public function label(): string
    {
        return 'Gravité moyenne des incidents';
    }

    public function target(): IndicatorTargetEnum
    {
        return IndicatorTargetEnum::Incidents;
    }

    public function outputType(): IndicatorOutputTypeEnum
    {
        return IndicatorOutputTypeEnum::Scalar;
    }

    public function parametersSchema(): ?array
    {
        return null;
    }

    public function compute(array $dataset, array $parameters = []): mixed
    {
        return round(collect($dataset)->avg('gravite') ?? 0, 2);
    }
}
