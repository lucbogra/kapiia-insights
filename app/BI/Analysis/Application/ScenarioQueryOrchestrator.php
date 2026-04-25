<?php

// app/BI/Analysis/Application/ScenarioQueryOrchestrator.php

namespace App\BI\Analysis\Application;

use App\BI\Analysis\Domain\ArchetypePopulationFilter;
use App\BI\Analysis\Infrastructure\Persistence\KapiiaSourceRepository;
use App\BI\DataSource\Domain\SourceConnection;
use App\BI\DataSource\Infrastructure\DynamicConnectionFactory;
use Illuminate\Support\Collection;

final class ScenarioQueryOrchestrator
{
    public function __construct(
        private readonly DynamicConnectionFactory $connectionFactory,
    ) {}

    /**
     * @param  SourceConnection[]  $sources
     * @param  array  $precisions  [['precision' => PrecisionInterface, 'parameters' => array], ...]
     * @return array{
     *   population_count: int,
     *   incidents: Collection,
     *   source_labels: string[],
     * }
     */
    public function execute(
        ArchetypePopulationFilter $filter,
        array $precisions,
        array $sources,
    ): array {
        $totalPopulation = 0;
        $allIncidents = collect();
        $sourceLabels = [];

        foreach ($sources as $source) {
            $connection = $this->connectionFactory->make($source);

            $repo = new KapiiaSourceRepository(
                connection: $connection,
                sourceLabel: $source->label,
            );

            $totalPopulation += $repo->countPopulationWithPrecisions($filter, $precisions);
            $allIncidents = $allIncidents->merge($repo->getIncidentsWithPrecisions($filter, $precisions));
            $sourceLabels[] = $source->label;
        }

        return [
            'population_count' => $totalPopulation,
            'incidents' => $allIncidents,
            'source_labels' => $sourceLabels,
        ];
    }
}
