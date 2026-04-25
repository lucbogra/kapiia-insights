<?php

namespace App\BI\Analysis\Infrastructure\Persistence;

use App\BI\Analysis\Domain\Repository\ScenarioRepositoryInterface;
use App\BI\Analysis\Domain\Scenario;
use App\BI\Analysis\Domain\ScenarioIndicatorConfig;
use App\BI\Analysis\Domain\ScenarioPrecisionConfig;
use Illuminate\Support\Facades\DB;

class ScenarioRepository implements ScenarioRepositoryInterface
{
    public function findById(string $id): ?Scenario
    {
        $model = ScenarioModel::with(['precisions', 'indicators', 'sourceConnections'])
            ->find($id);

        return $model ? $this->toDomain($model) : null;
    }

    public function findVisibleTo(string $userId): array
    {
        return ScenarioModel::with(['precisions', 'indicators', 'sourceConnections'])
            ->where(function ($q) use ($userId) {
                $q->where('owner_id', $userId)
                    ->orWhere('is_shared', true);
            })
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn ($m) => $this->toDomain($m))
            ->all();
    }

    public function findOwnedBy(string $userId): array
    {
        return ScenarioModel::with(['precisions', 'indicators', 'sourceConnections'])
            ->where('owner_id', $userId)
            ->orderByDesc('updated_at')
            ->get()
            ->map(fn ($m) => $this->toDomain($m))
            ->all();
    }

    public function save(Scenario $scenario): void
    {
        DB::transaction(function () use ($scenario) {
            ScenarioModel::updateOrCreate(
                ['id' => $scenario->id],
                [
                    'name' => $scenario->name,
                    'description' => $scenario->description,
                    'owner_id' => $scenario->ownerId,
                    'is_shared' => $scenario->isShared,
                    'archetype_id' => $scenario->archetypeId,
                    'criteria_values' => $scenario->criteriaValues,
                ],
            );

            $this->syncPrecisions($scenario);
            $this->syncIndicators($scenario);
            // $this->syncSources($scenario);
        });
    }

    public function delete(string $id): void
    {
        ScenarioModel::destroy($id);
    }

    private function syncPrecisions(Scenario $scenario): void
    {
        ScenarioPrecisionModel::where('scenario_id', $scenario->id)->delete();

        foreach ($scenario->precisions as $config) {
            ScenarioPrecisionModel::create([
                'scenario_id' => $scenario->id,
                'precision_definition_id' => $config->precisionDefinitionId,
                'parameters' => $config->parameters,
                'sort_order' => $config->sortOrder,
            ]);
        }
    }

    private function syncIndicators(Scenario $scenario): void
    {
        ScenarioIndicatorModel::where('scenario_id', $scenario->id)->delete();

        foreach ($scenario->indicators as $config) {
            ScenarioIndicatorModel::create([
                'scenario_id' => $scenario->id,
                'indicator_definition_id' => $config->indicatorDefinitionId,
                'parameters' => $config->parameters,
                'sort_order' => $config->sortOrder,
            ]);
        }
    }

    private function syncSources(Scenario $scenario): void
    {
        ScenarioModel::find($scenario->id)
            ?->sourceConnections()
            ->sync($scenario->sourceConnectionIds);
    }

    private function toDomain(ScenarioModel $m): Scenario
    {
        $precisions = $m->precisions->map(fn ($p) => new ScenarioPrecisionConfig(
            precisionDefinitionId: $p->precision_definition_id,
            parameters: $p->parameters ?? [],
            sortOrder: $p->sort_order,
        ))->all();

        $indicators = $m->indicators->map(fn ($i) => new ScenarioIndicatorConfig(
            indicatorDefinitionId: $i->indicator_definition_id,
            parameters: $i->parameters ?? [],
            sortOrder: $i->sort_order,
        ))->all();

        return new Scenario(
            id: $m->id,
            name: $m->name,
            description: $m->description,
            ownerId: (string) $m->owner_id,
            isShared: $m->is_shared,
            archetypeId: $m->archetype_id,
            criteriaValues: $m->criteria_values,
            precisions: $precisions,
            indicators: $indicators,
            sourceConnectionIds: $m->sourceConnections->pluck('id')->all(),
        );
    }
}
