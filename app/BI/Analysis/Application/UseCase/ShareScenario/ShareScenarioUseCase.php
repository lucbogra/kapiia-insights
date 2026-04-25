<?php

namespace App\BI\Analysis\Application\UseCase\ShareScenario;

use App\BI\Analysis\Domain\Repository\ScenarioRepositoryInterface;
use App\BI\Analysis\Domain\Scenario;

final class ShareScenarioUseCase
{
    public function __construct(
        private readonly ScenarioRepositoryInterface $repository,
    ) {}

    public function execute(string $scenarioId, string $requesterId, bool $share): Scenario
    {
        $scenario = $this->repository->findById($scenarioId);

        if (! $scenario) {
            throw new \DomainException("Scénario introuvable : {$scenarioId}");
        }

        if (! $scenario->isOwnedBy($requesterId)) {
            throw new \DomainException('Seul le propriétaire peut partager un scénario.');
        }

        $updated = new Scenario(
            id: $scenario->id,
            name: $scenario->name,
            description: $scenario->description,
            ownerId: $scenario->ownerId,
            isShared: $share,
            archetypeId: $scenario->archetypeId,
            criteriaValues: $scenario->criteriaValues,
            precisions: $scenario->precisions,
            indicators: $scenario->indicators,
            sourceConnectionIds: $scenario->sourceConnectionIds,
        );

        $this->repository->save($updated);

        return $updated;
    }
}
