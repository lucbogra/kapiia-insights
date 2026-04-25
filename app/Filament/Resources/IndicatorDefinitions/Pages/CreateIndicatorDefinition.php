<?php

namespace App\Filament\Resources\IndicatorDefinitions\Pages;

use App\BI\Analysis\Domain\Indicator\IndicatorRegistry;
use App\Filament\Resources\IndicatorDefinitions\IndicatorDefinitionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateIndicatorDefinition extends CreateRecord
{
    protected static string $resource = IndicatorDefinitionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $indicator = app(IndicatorRegistry::class)->get($data['key']);

        if (! $indicator) {
            throw new \DomainException("Indicateur introuvable en code : {$data['key']}");
        }

        $data['target'] = $indicator->target()->value;       // string pour la BD
        $data['output_type'] = $indicator->outputType()->value;
        $data['parameters_schema'] = $indicator->parametersSchema();

        return $data;
    }
}
