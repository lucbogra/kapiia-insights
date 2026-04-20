<?php

namespace App\Filament\Resources\SourceConnections\Pages;

use App\Filament\Resources\SourceConnections\SourceConnectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSourceConnections extends ListRecords
{
    protected static string $resource = SourceConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
