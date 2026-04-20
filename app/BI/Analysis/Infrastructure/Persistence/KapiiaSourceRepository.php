<?php

namespace App\BI\Analysis\Infrastructure\Persistence;

use App\BI\Analysis\Application\DTO\IncidentData;
use App\BI\Analysis\Application\DTO\TransmissionData;
use App\BI\Analysis\Domain\ArchetypePopulationFilter;
use App\BI\Profiling\Domain\Criteria\CriterionInterface;
use App\BI\Profiling\Domain\Criteria\DiscreteCriterion;
use App\BI\Profiling\Domain\Criteria\RangeCriterion;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;

final class KapiiaSourceRepository
{
    public function __construct(
        private readonly ConnectionInterface $connection,
        private readonly string              $sourceLabel,
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
                'incident_attitudes.attitude as attitude'
            ])
            ->where($this->populationConstraints($filter))
            ->get()
            ->map(fn($row) => new IncidentData(
                intitule:       $row->intitule,
                gravite:        (int) $row->gravite,
                dateIncident:   $row->date_incident,
                lieu:           $row->lieu,
                attitude:       $row->attitude,
                sourceLabel:    $this->sourceLabel
            ));
    }

    /**
     * @return Collection<TransmissionData>
     */
    public function getTransmissionIndicators(ArchetypePopulationFilter $filter): Collection
    {
        return $this->connection
            ->table('transmission_activities')
            ->join('transmissions', 'transmission_activities.transmission_id', '=', 'transmissions.id')
            ->join('users', 'transmissions.user_id', '=', 'users.id')
            ->select([
                'transmission_activities.activity_slug',
                'transmission_activities.concentration',
                'transmission_activities.behavior',
                'transmission_activities.social',
                'transmissions.transmitted_at',
            ])
            ->where($this->populationConstraints($filter))
            ->get()
            ->map(fn($row) => new TransmissionData(
                activitySlug:  $row->activity_slug,
                concentration: (int) $row->concentration,
                behavior:      (int) $row->behavior,
                social:        (int) $row->social,
                transmittedAt: $row->transmitted_at,
                sourceLabel:   $this->sourceLabel,
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
            $criterion instanceof RangeCriterion    => $query->whereBetween(
                'usagers_view.' . $criterion->column(),
                [$criterion->min(), $criterion->max()],
            ),
            $criterion instanceof DiscreteCriterion => $query->where(
                'usagers_view.' . $criterion->column(),
                $criterion->value(),
            ),
            default => null,
        };
    }
}
