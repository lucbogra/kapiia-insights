<?php

namespace App\BI\Analysis\Application\UseCase\UpdateScenario;

use App\BI\Analysis\Domain\Repository\ScenarioRepositoryInterface;
use App\BI\Analysis\Domain\Scenario;
use App\BI\Analysis\Domain\ScenarioIndicatorConfig;
use App\BI\Analysis\Domain\ScenarioPrecisionConfig;
use App\BI\Analysis\Domain\Service\PopulationSourceResolver;

final class UpdateScenarioUseCase
{
    public function __construct(
        private readonly ScenarioRepositoryInterface $repository,
        private readonly PopulationSourceResolver $populationResolver,
    ) {}

    public function execute(UpdateScenarioCommand $command): Scenario
    {
        $existing = $this->repository->findById($command->scenarioId);

        if (! $existing) {
            throw new \DomainException("Scénario introuvable : {$command->scenarioId}");
        }

        if (! $existing->isOwnedBy($command->requesterId)) {
            throw new \DomainException('Seul le propriétaire peut modifier un scénario.');
        }

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

        $updated = new Scenario(
            id: $existing->id,
            name: $command->name,
            description: $command->description,
            ownerId: $existing->ownerId,
            isShared: $command->isShared,
            archetypeId: $archetypeId,
            criteriaValues: $criteriaValues,
            precisions: $precisions,
            indicators: $indicators,
            sourceConnectionIds: $command->sourceConnectionIds,
        );

        $this->repository->save($updated);

        return $updated;
    }
}
