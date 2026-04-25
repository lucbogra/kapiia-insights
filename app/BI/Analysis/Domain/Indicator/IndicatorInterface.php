<?php

namespace App\BI\Analysis\Domain\Indicator;

interface IndicatorInterface
{
    public function key(): string;

    public function label(): string;

    public function target(): IndicatorTargetEnum;

    public function outputType(): IndicatorOutputTypeEnum;

    public function parametersSchema(): ?array;

    public function compute(array $dataset, array $parameters = []): mixed;
}
