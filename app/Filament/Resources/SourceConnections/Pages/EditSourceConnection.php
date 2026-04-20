<?php

namespace App\Filament\Resources\SourceConnections\Pages;

use App\Filament\Resources\SourceConnections\SourceConnectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSourceConnection extends EditRecord
{
    protected static string $resource = SourceConnectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
