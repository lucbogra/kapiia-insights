<?php

namespace App\BI\Profiling\Application\UseCase\CreateArchetype;

use App\BI\Profiling\Domain\Archetype;
use App\BI\Profiling\Domain\Repository\ArchetypeRepositoryInterface;
use App\BI\Profiling\Domain\Repository\ArchetypeCriterionRepositoryInterface;
use App\BI\Profiling\Domain\Service\NomenclatureGenerator;
use Illuminate\Support\Str;

final class CreateArchetypeUseCase
{
    public function __construct(
        private readonly ArchetypeRepositoryInterface          $archetypeRepository,
        private readonly ArchetypeCriterionRepositoryInterface $criterionRepository,
        private readonly NomenclatureGenerator                 $nomenclatureGenerator,
    ) {}

    public function execute(CreateArchetypeCommand $command): Archetype
    {
        $hash = Archetype::hashCriteria($command->criteriaValues);

        // Vérifier qu'un archétype identique n'existe pas déjà
        $existing = $this->archetypeRepository->findByHash($hash);

        if ($existing) {
            throw new \DomainException(
                "Un archétype identique existe déjà : {$existing->nomenclature}"
            );
        }

        // Récupérer les critères triés pour la génération de nomenclature
        $criteria = $this->criterionRepository->findAll();

        usort($criteria, fn($a, $b) => $a->sortOrder <=> $b->sortOrder);

        $nomenclature = $this->nomenclatureGenerator->generate(
            $criteria,
            $command->criteriaValues,
        );

        $archetype = new Archetype(
            id:             (string) Str::ulid(),
            criteriaValues: $command->criteriaValues,
            criteriaHash:   $hash,
            nomenclature:   $nomenclature,
            description:    $command->description,
            isActive:       true,
        );

        $this->archetypeRepository->save($archetype);

        return $archetype;
    }
}
