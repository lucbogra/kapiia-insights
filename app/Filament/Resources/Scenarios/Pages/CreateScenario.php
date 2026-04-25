<?php

namespace App\Filament\Resources\Scenarios\Pages;

use App\BI\Analysis\Application\UseCase\CreateScenario\CreateScenarioCommand;
use App\BI\Analysis\Application\UseCase\CreateScenario\CreateScenarioUseCase;
use App\BI\Analysis\Infrastructure\Persistence\ScenarioModel;
use App\Filament\Resources\Scenarios\ScenarioResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateScenario extends CreateRecord
{
    protected static string $resource = ScenarioResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $mode = $data['population_mode'] ?? 'archetype';

        if ($mode === 'archetype') {
            $data['criteria_values'] = null;
        } else {
            $data['archetype_id'] = null;
        }

        $useCase = app(CreateScenarioUseCase::class);

        $scenario = $useCase->execute(new CreateScenarioCommand(
            name: $data['name'],
            ownerId: (string) Auth::id(),
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

        return ScenarioModel::find($scenario->id);
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
