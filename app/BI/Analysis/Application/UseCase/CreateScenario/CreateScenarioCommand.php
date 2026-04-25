<?php

// app/BI/Analysis/Application/UseCase/CreateScenario/CreateScenarioCommand.php

namespace App\BI\Analysis\Application\UseCase\CreateScenario;

final class CreateScenarioCommand
{
    /**
     * @param  array<int, array{precision_definition_id: string, parameters: array, sort_order?: int}>  $precisions
     * @param  array<int, array{indicator_definition_id: string, parameters: array, sort_order?: int}>  $indicators
     * @param  string[]  $sourceConnectionIds
     */
    public function __construct(
        public readonly string $name,
        public readonly string $ownerId,
        public readonly ?string $description = null,
        public readonly bool $isShared = false,
        public readonly ?string $archetypeId = null,
        public readonly ?array $criteriaValues = null,
        public readonly array $precisions = [],
        public readonly array $indicators = [],
        public readonly array $sourceConnectionIds = [],
    ) {}
}
