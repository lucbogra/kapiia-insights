<?php

// app/BI/Analysis/Application/ResultAggregator.php

namespace App\BI\Analysis\Application;

use Illuminate\Support\Collection;

final class ResultAggregator
{
    /**
     * @param  array  $partialResults  Résultats bruts par source
     */
    public function aggregate(Collection $partialResults): array
    {
        $population = collect($partialResults)->sum('population');

        $allIncidents = collect($partialResults)
            ->flatMap(fn ($r) => $r['incidents']);

        // dd($allIncidents);

        return [
            'population_count' => $population,
            'sources' => collect($partialResults)->pluck('source')->all(),

            // Incidents
            'incidents_total' => $allIncidents->count(),
            'average_gravite' => round($allIncidents->avg('gravite') ?? 0, 2),
            'incidents_by_title' => $allIncidents
                ->groupBy('intitule')
                ->map->count()
                ->sortDesc()
                ->all(),
            'gravite_distribution' => $allIncidents
                ->groupBy('gravite')
                ->map->count()
                ->all(),
        ];
    }
}
