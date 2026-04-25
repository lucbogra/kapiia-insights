<?php

namespace App\BI\Analysis\Application\UseCase\DeleteScenario;

use App\BI\Analysis\Domain\Repository\ScenarioRepositoryInterface;

final class DeleteScenarioUseCase
{
    public function __construct(
        private readonly ScenarioRepositoryInterface $repository,
    ) {}

    public function execute(string $scenarioId, string $requesterId): void
    {
        $scenario = $this->repository->findById($scenarioId);

        if (! $scenario) {
            throw new \DomainException("Scénario introuvable : {$scenarioId}");
        }

        if (! $scenario->isOwnedBy($requesterId)) {
            throw new \DomainException('Seul le propriétaire peut supprimer un scénario.');
        }

        $this->repository->delete($scenarioId);
    }
}
