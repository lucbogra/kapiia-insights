<?php

namespace App\Filament\Resources\Archetypes\Pages;

use App\Filament\Resources\Archetypes\ArchetypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditArchetype extends EditRecord
{
    protected static string $resource = ArchetypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
