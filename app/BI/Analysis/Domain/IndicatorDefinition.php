<?php

namespace App\BI\Analysis\Domain;

use App\BI\Analysis\Domain\Indicator\IndicatorOutputTypeEnum;
use App\BI\Analysis\Domain\Indicator\IndicatorTargetEnum;

final class IndicatorDefinition
{
    public function __construct(
        public readonly string $id,
        public readonly string $key,
        public readonly string $label,
        public readonly ?string $description,
        public readonly IndicatorTargetEnum $target,         // 'incidents' | 'transmissions' | 'global'
        public readonly IndicatorOutputTypeEnum $outputType,     // 'scalar' | 'list' | 'distribution' | 'grouped_average'
        public readonly ?array $parametersSchema,
        public readonly bool $isActive,
        public readonly int $sortOrder,
    ) {}

    public function targetsIncidents(): bool
    {
        return $this->target === IndicatorTargetEnum::Incidents;
    }

    public function targetsTransmissions(): bool
    {
        return $this->target === IndicatorTargetEnum::Transmissions;
    }

    public function requiresParameters(): bool
    {
        return ! empty($this->parametersSchema);
    }
}
