<?php

namespace App\Filament\Resources\ArchetypeCriteria\Pages;

use App\Filament\Resources\ArchetypeCriteria\ArchetypeCriterionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListArchetypeCriteria extends ListRecords
{
    protected static string $resource = ArchetypeCriterionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
