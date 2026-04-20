<?php

namespace App\Filament\Resources\Archetypes\Pages;

use App\BI\Profiling\Application\UseCase\CreateArchetype\CreateArchetypeCommand;
use App\BI\Profiling\Application\UseCase\CreateArchetype\CreateArchetypeUseCase;
use App\BI\Profiling\Infrastructure\Persistence\ArchetypeModel;
use App\Filament\Resources\Archetypes\ArchetypeResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;
use Illuminate\Validation\ValidationException;

class CreateArchetype extends CreateRecord
{
    protected static string $resource = ArchetypeResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $useCase = app(CreateArchetypeUseCase::class);

        try {
            $domain = $useCase->execute(new CreateArchetypeCommand(
                criteriaValues: $data['criteria_values'] ?? [],
                description:    $data['description'] ?? null,
            ));

            return ArchetypeModel::find($domain->id);

        } catch(\Throwable $e) {

            Notification::make()
                ->title('Erreur de création')
                ->body($e->getMessage())
                ->danger()
                ->send();

            throw new Halt();
        }

    }
}
