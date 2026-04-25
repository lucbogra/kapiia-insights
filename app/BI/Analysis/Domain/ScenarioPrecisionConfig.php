<?php

namespace App\BI\Analysis\Domain;

final class ScenarioPrecisionConfig
{
    public function __construct(
        public readonly string $precisionDefinitionId,
        public readonly array $parameters,
        public readonly int $sortOrder = 0,
    ) {}
}
