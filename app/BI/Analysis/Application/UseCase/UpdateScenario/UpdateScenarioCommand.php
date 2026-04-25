<?php

namespace App\BI\Analysis\Application\UseCase\UpdateScenario;

final class UpdateScenarioCommand
{
    /**
     * @param  array<int, array{precision_definition_id: string, parameters: array, sort_order?: int}>  $precisions
     * @param  array<int, array{indicator_definition_id: string, parameters: array, sort_order?: int}>  $indicators
     * @param  string[]  $sourceConnectionIds
     */
    public function __construct(
        public readonly string $scenarioId,
        public readonly string $requesterId,
        public readonly string $name,
        public readonly ?string $description = null,
        public readonly bool $isShared = false,
        public readonly ?string $archetypeId = null,
        public readonly ?array $criteriaValues = null,
        public readonly array $precisions = [],
        public readonly array $indicators = [],
        public readonly array $sourceConnectionIds = [],
    ) {}
}
