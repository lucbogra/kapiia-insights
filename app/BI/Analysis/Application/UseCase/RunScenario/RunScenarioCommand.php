<?php

namespace App\BI\Analysis\Application\UseCase\RunScenario;

final class RunScenarioCommand
{
    public function __construct(
        public readonly string $scenarioId,
        public readonly string $requesterId,
    ) {}
}
