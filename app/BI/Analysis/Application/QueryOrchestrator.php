<?php

namespace App\BI\Analysis\Application;

use App\BI\Analysis\Application\DTO\IncidentData;
use App\BI\Analysis\Domain\ArchetypePopulationFilter;
use App\BI\Analysis\Infrastructure\Persistence\KapiiaSourceRepository;
use App\BI\DataSource\Domain\SourceConnection;
use App\BI\DataSource\Infrastructure\DynamicConnectionFactory;
use Illuminate\Support\Collection;

final class QueryOrchestrator
{
    public function __construct(
        private readonly DynamicConnectionFactory $connectionFactory,
    ) {}

    /**
     * Exécute les queries sur chaque source et retourne les résultats bruts.
     *
     * @param  SourceConnection[]  $sources
     * @return Collection<int, array{
     *     source:         string,
     *     population:     int,
     *     incidents:      Collection<IncidentData>,
     * }>
     */
    public function execute(ArchetypePopulationFilter $filter, array $sources): Collection
    {
        return collect($sources)->map(
            fn (SourceConnection $source) => $this->querySource($source, $filter)
        );
    }

    /**
     * Interroge une source unique et retourne ses résultats.
     *
     * @return array{
     *     source:        string,
     *     population:    int,
     *     incidents:     Collection<IncidentData>,
     * }
     */
    private function querySource(SourceConnection $source, ArchetypePopulationFilter $filter): array
    {
        $connection = $this->connectionFactory->make($source);

        $repo = new KapiiaSourceRepository(
            connection: $connection,
            sourceLabel: $source->label,
        );

        return [
            'source' => $source->label,
            'population' => $repo->countPopulation($filter),
            'incidents' => $repo->getIncidents($filter),
        ];
    }
}
