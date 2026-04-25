<?php

namespace App\BI\Analysis\Application\UseCase\RunScenario;

use App\BI\Analysis\Application\ScenarioIndicatorCalculator;
use App\BI\Analysis\Application\ScenarioPopulationFilterBuilder;
use App\BI\Analysis\Application\ScenarioQueryOrchestrator;
use App\BI\Analysis\Domain\Indicator\IndicatorRegistry;
use App\BI\Analysis\Domain\Precision\PrecisionRegistry;
use App\BI\Analysis\Domain\Repository\IndicatorDefinitionRepositoryInterface;
use App\BI\Analysis\Domain\Repository\PrecisionDefinitionRepositoryInterface;
use App\BI\Analysis\Domain\Repository\ScenarioRepositoryInterface;
use App\BI\Analysis\Domain\ScenarioResult;
use App\BI\DataSource\Domain\Repository\SourceConnectionRepositoryInterface;

final class RunScenarioUseCase
{
    public function __construct(
        private readonly ScenarioRepositoryInterface $scenarioRepository,
        private readonly SourceConnectionRepositoryInterface $sourceRepository,
        private readonly PrecisionDefinitionRepositoryInterface $precisionDefRepository,
        private readonly IndicatorDefinitionRepositoryInterface $indicatorDefRepository,
        private readonly PrecisionRegistry $precisionRegistry,
        private readonly IndicatorRegistry $indicatorRegistry,
        private readonly ScenarioPopulationFilterBuilder $filterBuilder,
        private readonly ScenarioQueryOrchestrator $orchestrator,
        private readonly ScenarioIndicatorCalculator $calculator,
    ) {}

    public function execute(RunScenarioCommand $command): ScenarioResult
    {
        // 1. Charger le scénario et vérifier la visibilité
        $scenario = $this->scenarioRepository->findById($command->scenarioId);

        if (! $scenario) {
            throw new \DomainException("Scénario introuvable : {$command->scenarioId}");
        }

        if (! $scenario->isVisibleTo($command->requesterId)) {
            throw new \DomainException('Accès refusé à ce scénario.');
        }

        // 2. Résoudre le filtre de population (critères d'identité)
        $filter = $this->filterBuilder->build($scenario);

        // dd($filter);

        // 3. Résoudre les précisions : Registry + paramètres utilisateur
        $resolvedPrecisions = $this->resolvePrecisions($scenario);

        // 4. Résoudre les indicateurs : Registry + paramètres utilisateur
        $resolvedIndicators = $this->resolveIndicators($scenario);

        // 5. Charger les sources
        $sources = array_filter(
            array_map(
                fn ($id) => $this->sourceRepository->findById($id),
                $scenario->sourceConnectionIds,
            ),
        );

        if (empty($sources)) {
            throw new \DomainException('Aucune source valide associée au scénario.');
        }

        // 6. Exécuter les queries sur toutes les sources
        $dataset = $this->orchestrator->execute($filter, $resolvedPrecisions, $sources);

        // 7. Calculer les indicateurs sélectionnés
        $indicatorResults = $this->calculator->compute($resolvedIndicators, $dataset);

        return new ScenarioResult(
            scenarioId: $scenario->id,
            populationCount: $dataset['population_count'],
            incidentCount: $dataset['incidents']->count(),
            indicatorResults: $indicatorResults,
            sourceLabels: $dataset['source_labels'],
            computedAt: now()->toISOString(),
        );
    }

    /**
     * Transforme les configs de scénario en [precision, parameters] utilisables.
     */
    private function resolvePrecisions($scenario): array
    {
        $resolved = [];

        foreach ($scenario->precisions as $config) {
            $definition = $this->precisionDefRepository->findById($config->precisionDefinitionId);

            if (! $definition || ! $definition->isActive) {
                continue;
            }

            $precision = $this->precisionRegistry->get($definition->key);

            if (! $precision) {
                throw new \DomainException(
                    "Précision enregistrée en base mais absente du code : {$definition->key}"
                );
            }

            $resolved[] = [
                'precision' => $precision,
                'parameters' => $config->parameters,
            ];
        }

        return $resolved;
    }

    private function resolveIndicators($scenario): array
    {
        $resolved = [];

        foreach ($scenario->indicators as $config) {
            $definition = $this->indicatorDefRepository->findById($config->indicatorDefinitionId);

            if (! $definition || ! $definition->isActive) {
                continue;
            }

            $indicator = $this->indicatorRegistry->get($definition->key);

            if (! $indicator) {
                throw new \DomainException(
                    "Indicateur enregistré en base mais absent du code : {$definition->key}"
                );
            }

            $resolved[] = [
                'indicator' => $indicator,
                'definition' => $definition,
                'parameters' => $config->parameters,
            ];
        }

        return $resolved;
    }
}
