<?php

namespace App\BI\Analysis\Infrastructure\Persistence;

use App\BI\Analysis\Application\DTO\IncidentData;
use App\BI\Analysis\Domain\ArchetypePopulationFilter;
use App\BI\Analysis\Domain\Precision\PrecisionInterface;
use App\BI\Analysis\Domain\Precision\PrecisionTargetEnum;
use App\BI\Analysis\Domain\Precision\PrecisionTypeEnum;
use App\BI\Profiling\Domain\Criteria\CriterionInterface;
use App\BI\Profiling\Domain\Criteria\DiscreteCriterion;
use App\BI\Profiling\Domain\Criteria\RangeCriterion;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;

final class KapiiaSourceRepository
{
    public function __construct(
        private readonly ConnectionInterface $connection,
        private readonly string $sourceLabel,
    ) {}

    /**
     * @return Collection<IncidentData>
     */
    public function getIncidents(ArchetypePopulationFilter $filter): Collection
    {
        return $this->connection
            ->table('incidents')
            ->join('usagers_view', 'incidents.usager_id', '=', 'usagers_view.id')
            ->join('incident_intitules', 'incidents.incident_intitule_id', '=', 'incident_intitules.id')
            ->join('incident_attitudes', 'incidents.incident_attitude_id', '=', 'incident_attitudes.id')
            ->select([
                'incident_intitules.intitule as intitule',
                'incidents.gravite',
                'incidents.date_incident',
                'incidents.lieu',
                'incident_attitudes.attitude as attitude',
            ])
            ->where($this->populationConstraints($filter))
            ->get()
            ->map(fn ($row) => new IncidentData(
                intitule: $row->intitule,
                gravite: (int) $row->gravite,
                dateIncident: $row->date_incident,
                lieu: $row->lieu,
                attitude: $row->attitude,
                sourceLabel: $this->sourceLabel
            ));
    }

    /**
     * Retourne le nombre d'usagers correspondant au filtre.
     * Utilisé pour un count précis indépendamment des incidents.
     */
    public function countPopulation(ArchetypePopulationFilter $filter): int
    {
        return (int) $this->connection
            ->table('usagers_view')
            ->where($this->populationConstraints($filter))
            ->count();
    }

    /**
     * Construit la closure WHERE à partir des critères du filtre.
     * Gère les deux types : discret et plage.
     */
    private function populationConstraints(ArchetypePopulationFilter $filter): \Closure
    {
        return function ($query) use ($filter) {
            foreach ($filter->criteria() as $criterion) {
                $this->applyCriterion($query, $criterion);
            }
        };
    }

    /**
     * Applique un critère unique à la query.
     * Le switch sur le type concret garantit l'exhaustivité.
     */
    private function applyCriterion($query, CriterionInterface $criterion): void
    {
        match (true) {
            $criterion instanceof RangeCriterion => $this->applyRangeCriterion($query, $criterion),
            $criterion instanceof DiscreteCriterion => $query->where(
                'usagers_view.'.$criterion->column(),
                $criterion->value(),
            ),
            default => null,
        };
    }

    /**
     * Applique un critère de plage à la query.
     * Supporte les plages partielles (borne min ou max absente).
     */
    private function applyRangeCriterion($query, RangeCriterion $criterion): void
    {
        $column = 'usagers_view.'.$criterion->column();
        $min = $criterion->min();
        $max = $criterion->max();

        if ($min !== null && $max !== null) {
            $query->whereBetween($column, [$min, $max]);
        } elseif ($min !== null) {
            $query->where($column, '>=', $min);
        } elseif ($max !== null) {
            $query->where($column, '<=', $max);
        }
        // Les deux bornes absentes : le sanitiseur garantit que ce cas n'arrive jamais.
    }

    /**
     * @param  array<int, array{precision: PrecisionInterface, parameters: array}>  $precisions
     */
    public function getIncidentsWithPrecisions(
        ArchetypePopulationFilter $filter,
        array $precisions,
    ): Collection {
        $query = $this->connection
            ->table('incidents')
            ->join('usagers_view', 'incidents.usager_id', '=', 'usagers_view.id')
            ->join('incident_intitules', 'incidents.incident_intitule_id', '=', 'incident_intitules.id')
            ->join('incident_attitudes', 'incidents.incident_attitude_id', '=', 'incident_attitudes.id')
            ->select([
                'incident_intitules.intitule as intitule',
                'incidents.gravite',
                'incidents.date_incident',
                'incidents.lieu',
                'incident_attitudes.attitude as attitude',
            ])
            ->where($this->populationConstraints($filter));

        $this->applyPrecisionsToQuery($query, $precisions, PrecisionTargetEnum::Incidents);

        return $query->get()->map(fn ($row) => new IncidentData(
            intitule: $row->intitule,
            gravite: (int) $row->gravite,
            dateIncident: $row->date_incident,
            lieu: $row->lieu,
            attitude: $row->attitude,
            sourceLabel: $this->sourceLabel,
        ));
    }

    public function countPopulationWithPrecisions(
        ArchetypePopulationFilter $filter,
        array $precisions,
    ): int {
        $query = $this->connection
            ->table('usagers_view')
            ->where($this->populationConstraints($filter));

        // Seules les précisions de type PopulationFilter affectent le count
        $populationPrecisions = array_filter(
            $precisions,
            fn ($p) => $p['precision']->type() === PrecisionTypeEnum::PopulationFilter,
        );

        $this->applyPrecisionsToQuery($query, $populationPrecisions, null);

        return (int) $query->count();
    }

    /**
     * Applique les précisions pertinentes à une query.
     * Si $target est null, applique toutes les précisions fournies.
     * Sinon, applique seulement celles qui matchent le target.
     */
    private function applyPrecisionsToQuery(
        $query,
        array $precisions,
        ?PrecisionTargetEnum $target,
    ): void {
        foreach ($precisions as $entry) {
            $precision = $entry['precision'];

            if ($target !== null && $precision->target() !== $target) {
                continue;
            }

            $precision->apply($query, $entry['parameters']);
        }
    }
}
