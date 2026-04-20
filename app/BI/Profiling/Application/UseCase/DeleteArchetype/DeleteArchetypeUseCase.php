<?php

namespace App\BI\Profiling\Application\UseCase\DeleteArchetype;

use App\BI\Profiling\Domain\Repository\ArchetypeRepositoryInterface;

final class DeleteArchetypeUseCase
{
    public function __construct(
        private readonly ArchetypeRepositoryInterface $repository,
    ) {}

    public function execute(string $id): void
    {
        $archetype = $this->repository->findById($id);

        if (! $archetype) {
            throw new \DomainException("Archétype introuvable : {$id}");
        }

        $this->repository->delete($id);
    }
}
