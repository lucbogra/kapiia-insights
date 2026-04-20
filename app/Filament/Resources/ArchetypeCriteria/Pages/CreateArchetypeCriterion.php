<?php

namespace App\Filament\Resources\ArchetypeCriteria\Pages;

use App\BI\Profiling\Application\UseCase\CreateArchetypeCriterion\CreateArchetypeCriterionCommand;
use App\BI\Profiling\Application\UseCase\CreateArchetypeCriterion\CreateArchetypeCriterionUseCase;
use App\Filament\Resources\ArchetypeCriteria\ArchetypeCriterionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArchetypeCriterion extends CreateRecord
{
    protected static string $resource = ArchetypeCriterionResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $useCase = app(CreateArchetypeCriterionUseCase::class);

        $domain = $useCase->execute(new CreateArchetypeCriterionCommand(
            key:                $data['key'],
            label:              $data['label'],
            type:               $data['type'],
            options:            $data['options'] ?? [],
            sourceColumn:       $data['source_column'],
            nomenclaturePrefix: $data['nomenclature_prefix'] ?? null,
            sortOrder:          (int) ($data['sort_order'] ?? 0),
        ));

        return \App\BI\Profiling\Infrastructure\Persistence\ArchetypeCriterionModel::find($domain->id);
    }
}
