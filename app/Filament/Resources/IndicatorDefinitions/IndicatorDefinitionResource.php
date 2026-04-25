<?php

namespace App\Filament\Resources\IndicatorDefinitions;

use App\BI\Analysis\Infrastructure\Persistence\IndicatorDefinitionModel;
use App\Filament\Resources\IndicatorDefinitions\Pages\EditIndicatorDefinition;
use App\Filament\Resources\IndicatorDefinitions\Pages\ListIndicatorDefinitions;
use App\Filament\Resources\IndicatorDefinitions\Schemas\IndicatorDefinitionForm;
use App\Filament\Resources\IndicatorDefinitions\Tables\IndicatorDefinitionsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class IndicatorDefinitionResource extends Resource
{
    protected static ?string $model = IndicatorDefinitionModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartPie;

    protected static ?string $recordTitleAttribute = 'Indicateurs';

    protected static ?string $modelLabel = 'Indicateur';

    protected static ?string $pluralModelLabel = 'Indicateurs';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return IndicatorDefinitionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IndicatorDefinitionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIndicatorDefinitions::route('/'),
            'edit' => EditIndicatorDefinition::route('/{record}/edit'),
        ];
    }
}
