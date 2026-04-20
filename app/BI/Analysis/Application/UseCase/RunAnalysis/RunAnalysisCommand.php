<?php

namespace App\BI\Analysis\Application\UseCase\RunAnalysis;

final class RunAnalysisCommand
{
    public function __construct(
        public readonly string $archetypeId,
        /** @var string[] $sourceConnectionIds */
        public readonly array  $sourceConnectionIds,
        public readonly int    $cacheTtlHours = 24,
    ) {}
}
