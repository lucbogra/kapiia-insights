<?php

namespace App\Filament\Resources\Archetypes\Pages;

use App\Filament\Resources\Archetypes\ArchetypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArchetypes extends ListRecords
{
    protected static string $resource = ArchetypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
