<?php

namespace App\Filament\Resources\Scenarios\Pages;

use App\BI\Analysis\Application\UseCase\UpdateScenario\UpdateScenarioCommand;
use App\BI\Analysis\Application\UseCase\UpdateScenario\UpdateScenarioUseCase;
use App\Filament\Resources\Scenarios\ScenarioResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EditScenario extends EditRecord
{
    protected static string $resource = ScenarioResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        // Mode de population
        $data['population_mode'] = ! empty($data['archetype_id'])
            ? 'archetype'
            : 'custom';

        // Précisions liées
        $data['precisions'] = $record->precisions
            ->map(fn ($p) => [
                'precision_definition_id' => $p->precision_definition_id,
                'parameters' => $p->parameters ?? [],
                'sort_order' => $p->sort_order,
            ])
            ->values()
            ->all();

        // Indicateurs liés
        $data['indicators'] = $record->indicators
            ->map(fn ($i) => [
                'indicator_definition_id' => $i->indicator_definition_id,
                'parameters' => $i->parameters ?? [],
                'sort_order' => $i->sort_order,
            ])
            ->values()
            ->all();

        // Sources liées
        $data['sourceConnections'] = $record->sourceConnections
            ->pluck('id')
            ->all();

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Normalisation selon le mode avant d'appeler le Use Case
        $mode = $data['population_mode'] ?? 'archetype';

        if ($mode === 'archetype') {
            $data['criteria_values'] = null;
        } else {
            $data['archetype_id'] = null;
        }

        $useCase = app(UpdateScenarioUseCase::class);

        $useCase->execute(new UpdateScenarioCommand(
            scenarioId: $record->id,
            requesterId: (string) Auth::id(),
            name: $data['name'],
            description: $data['description'] ?? null,
            isShared: (bool) ($data['is_shared'] ?? false),
            archetypeId: $data['archetype_id'] ?? null,
            criteriaValues: $data['criteria_values'] ?? null,
            precisions: $this->mapRepeater(
                $data['precisions'] ?? [],
                'precision_definition_id',
            ),
            indicators: $this->mapRepeater(
                $data['indicators'] ?? [],
                'indicator_definition_id',
            ),
            sourceConnectionIds: $data['sourceConnections'] ?? [],
        ));

        return $record->fresh();
    }

    private function mapRepeater(array $items, string $definitionKey): array
    {
        return array_values(array_map(
            fn ($item) => [
                $definitionKey => $item[$definitionKey],
                'parameters' => $item['parameters'] ?? [],
                'sort_order' => (int) ($item['sort_order'] ?? 0),
            ],
            $items,
        ));
    }
}
