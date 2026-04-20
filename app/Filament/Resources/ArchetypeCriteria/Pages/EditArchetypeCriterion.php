<?php

namespace App\Filament\Resources\ArchetypeCriteria\Pages;

use App\Filament\Resources\ArchetypeCriteria\ArchetypeCriterionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditArchetypeCriterion extends EditRecord
{
    protected static string $resource = ArchetypeCriterionResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         DeleteAction::make(),
    //     ];
    // }
}
