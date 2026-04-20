<?php

// app/BI/Analysis/Application/UseCase/RunAnalysis/RunAnalysisUseCase.php

namespace App\BI\Analysis\Application\UseCase\RunAnalysis;

use App\BI\Analysis\Domain\AnalysisResult;
use App\BI\Analysis\Domain\Repository\AnalysisResultRepositoryInterface;
use App\BI\Analysis\Application\QueryOrchestrator;
use App\BI\Analysis\Application\ResultAggregator;
use App\BI\Analysis\Application\PopulationFilterBuilder;
use App\BI\DataSource\Domain\Repository\SourceConnectionRepositoryInterface;
use App\BI\Profiling\Domain\Repository\ArchetypeRepositoryInterface;
use App\BI\Profiling\Domain\Repository\ArchetypeCriterionRepositoryInterface;
use Illuminate\Support\Str;

final class RunAnalysisUseCase
{
    public function __construct(
        private readonly ArchetypeRepositoryInterface          $archetypeRepository,
        private readonly ArchetypeCriterionRepositoryInterface $criterionRepository,
        private readonly SourceConnectionRepositoryInterface   $sourceRepository,
        private readonly AnalysisResultRepositoryInterface     $resultRepository,
        private readonly QueryOrchestrator                     $orchestrator,
        private readonly ResultAggregator                      $aggregator,
        private readonly PopulationFilterBuilder               $filterBuilder,
    ) {}

    public function execute(RunAnalysisCommand $command): AnalysisResult
    {
        // 1. Vérifier si un cache frais existe déjà
        $cached = $this->resultRepository->findFreshByArchetype($command->archetypeId);

        if ($cached) {
            return $cached;
        }

        // 2. Charger l'archétype et les critères
        $archetype = $this->archetypeRepository->findById($command->archetypeId);

        if (! $archetype) {
            throw new \DomainException("Archétype introuvable : {$command->archetypeId}");
        }

        $criteria = $this->criterionRepository->findAll();

        // 3. Construire le filtre de population
        $filter = $this->filterBuilder->build($archetype->criteriaValues, $criteria);

        // 4. Charger les sources demandées
        $sources = array_filter(
            array_map(
                fn($id) => $this->sourceRepository->findById($id),
                $command->sourceConnectionIds,
            ),
        );

        if (empty($sources)) {
            throw new \DomainException('Aucune source valide trouvée.');
        }

        // 5. Exécuter les queries sur chaque source
        $partialResults = $this->orchestrator->execute($filter, $sources);

        // dd($partialResults);

        // 6. Agréger les résultats
        $payload = $this->aggregator->aggregate($partialResults);

        // 7. Persister le résultat en cache
        $result = new AnalysisResult(
            id:                  (string) Str::ulid(),
            archetypeId:         $command->archetypeId,
            sourceConnectionIds: $command->sourceConnectionIds,
            payload:             $payload,
            populationCount:     $payload['population_count'] ?? 0,
            expiresAt:           now()->addHours($command->cacheTtlHours)->toISOString(),
        );

        $this->resultRepository->save($result);

        return $result;
    }
}
