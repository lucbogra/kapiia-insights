<?php

namespace App\Filament\Resources\PrecisionDefinitions;

use App\BI\Analysis\Infrastructure\Persistence\PrecisionDefinitionModel;
use App\Filament\Resources\PrecisionDefinitions\Pages\EditPrecisionDefinition;
use App\Filament\Resources\PrecisionDefinitions\Pages\ListPrecisionDefinitions;
use App\Filament\Resources\PrecisionDefinitions\Schemas\PrecisionDefinitionForm;
use App\Filament\Resources\PrecisionDefinitions\Tables\PrecisionDefinitionsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PrecisionDefinitionResource extends Resource
{
    protected static ?string $model = PrecisionDefinitionModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFunnel;

    protected static ?string $recordTitleAttribute = 'Précision';

    protected static ?string $navigationLabel = 'Précisions';

    protected static ?string $modelLabel = 'Précision';

    protected static ?string $pluralModelLabel = 'Précisions';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return PrecisionDefinitionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PrecisionDefinitionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPrecisionDefinitions::route('/'),
            'edit' => EditPrecisionDefinition::route('/{record}/edit'),
        ];
    }
}
