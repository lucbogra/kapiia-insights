<?php

namespace App\BI\Analysis\Domain;

use App\BI\Analysis\Domain\Precision\PrecisionTargetEnum;
use App\BI\Analysis\Domain\Precision\PrecisionTypeEnum;

final class PrecisionDefinition
{
    public function __construct(
        public readonly string $id,
        public readonly string $key,
        public readonly string $label,
        public readonly ?string $description,
        public readonly PrecisionTypeEnum $type,
        public readonly PrecisionTargetEnum $target,
        public readonly ?array $parametersSchema,
        public readonly bool $isActive,
        public readonly int $sortOrder,
    ) {}

    public function isPopulationFilter(): bool
    {
        return $this->type === PrecisionTypeEnum::PopulationFilter;
    }

    public function isDatasetFilter(): bool
    {
        return $this->type === PrecisionTypeEnum::DatasetFilter;
    }
}
