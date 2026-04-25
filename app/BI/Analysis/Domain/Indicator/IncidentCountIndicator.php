<?php

namespace App\BI\Analysis\Domain\Indicator;

final class IncidentCountIndicator implements IndicatorInterface
{
    public function key(): string
    {
        return 'incident_count';
    }

    public function label(): string
    {
        return 'Nombre total d\'incidents';
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
        return count($dataset);
    }
}
