<?php

namespace App\BI\Profiling\Application\UseCase\CreateArchetype;

use App\BI\Profiling\Domain\Archetype;
use App\BI\Profiling\Domain\Repository\ArchetypeCriterionRepositoryInterface;
use App\BI\Profiling\Domain\Repository\ArchetypeRepositoryInterface;
use App\BI\Profiling\Domain\Service\CriteriaValuesSanitizer;
use App\BI\Profiling\Domain\Service\NomenclatureGenerator;
use Illuminate\Support\Str;

final class CreateArchetypeUseCase
{
    public function __construct(
        private readonly ArchetypeRepositoryInterface $archetypeRepository,
        private readonly ArchetypeCriterionRepositoryInterface $criterionRepository,
        private readonly NomenclatureGenerator $nomenclatureGenerator,
        private readonly CriteriaValuesSanitizer $sanitizer,
    ) {}

    public function execute(CreateArchetypeCommand $command): Archetype
    {
        $criteriaValues = $this->sanitizer->sanitize($command->criteriaValues);

        if (empty($criteriaValues)) {
            throw new \DomainException(
                'Un archétype doit contenir au moins un critère valorisé.'
            );
        }

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

        usort($criteria, fn ($a, $b) => $a->sortOrder <=> $b->sortOrder);

        $nomenclature = $this->nomenclatureGenerator->generate(
            $criteria,
            $criteriaValues,
        );

        $archetype = new Archetype(
            id: strtolower((string) Str::ulid()),
            criteriaValues: $criteriaValues,
            criteriaHash: $hash,
            nomenclature: $nomenclature,
            description: $command->description,
            isActive: true,
        );

        $this->archetypeRepository->save($archetype);

        return $archetype;
    }
}
