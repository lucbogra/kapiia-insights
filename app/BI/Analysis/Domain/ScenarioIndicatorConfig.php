<?php

namespace App\BI\Analysis\Domain;

final class ScenarioIndicatorConfig
{
    public function __construct(
        public readonly string $indicatorDefinitionId,
        public readonly array $parameters,
        public readonly int $sortOrder = 0,
    ) {}
}
