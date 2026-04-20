<?php

namespace App\Filament\Resources\SourceConnections\Pages;

use App\BI\DataSource\Application\SourceConnectionService;
use App\Filament\Resources\SourceConnections\SourceConnectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSourceConnection extends CreateRecord
{
    protected static string $resource = SourceConnectionResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $service = app(SourceConnectionService::class);

        $domain = $service->create(
            label:        $data['label'],
            host:         $data['host'],
            port:         (int) $data['port'],
            databaseName: $data['database_name'],
            username:     $data['username'],
            password:     $data['password'],
            driver:       $data['driver'],
        );

        // Retourner le modèle Eloquent attendu par Filament
        return \App\BI\DataSource\Infrastructure\Persistence\SourceConnectionModel::find($domain->id);
    }
}
