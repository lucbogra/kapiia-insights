<?php

// app/BI/Analysis/Application/UseCase/CreateScenario/CreateScenarioUseCase.php

namespace App\BI\Analysis\Application\UseCase\CreateScenario;

use App\BI\Analysis\Domain\Repository\ScenarioRepositoryInterface;
use App\BI\Analysis\Domain\Scenario;
use App\BI\Analysis\Domain\ScenarioIndicatorConfig;
use App\BI\Analysis\Domain\ScenarioPrecisionConfig;
use App\BI\Analysis\Domain\Service\PopulationSourceResolver;
use Illuminate\Support\Str;

final class CreateScenarioUseCase
{
    public function __construct(
        private readonly ScenarioRepositoryInterface $repository,
        private readonly PopulationSourceResolver $populationResolver,
    ) {}

    public function execute(CreateScenarioCommand $command): Scenario
    {
        [$archetypeId, $criteriaValues] = $this->populationResolver->resolve(
            $command->archetypeId,
            $command->criteriaValues,
        );

        $precisions = array_map(
            fn ($p) => new ScenarioPrecisionConfig(
                precisionDefinitionId: $p['precision_definition_id'],
                parameters: $p['parameters'] ?? [],
                sortOrder: $p['sort_order'] ?? 0,
            ),
            $command->precisions,
        );

        $indicators = array_map(
            fn ($i) => new ScenarioIndicatorConfig(
                indicatorDefinitionId: $i['indicator_definition_id'],
                parameters: $i['parameters'] ?? [],
                sortOrder: $i['sort_order'] ?? 0,
            ),
            $command->indicators,
        );

        $scenario = new Scenario(
            id: strtolower((string) Str::ulid()),
            name: $command->name,
            description: $command->description,
            ownerId: $command->ownerId,
            isShared: $command->isShared,
            archetypeId: $archetypeId,
            criteriaValues: $criteriaValues,
            precisions: $precisions,
            indicators: $indicators,
            sourceConnectionIds: $command->sourceConnectionIds,
        );

        $this->repository->save($scenario);

        return $scenario;
    }
}
