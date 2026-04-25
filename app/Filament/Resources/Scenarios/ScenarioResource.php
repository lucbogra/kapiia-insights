<?php

namespace App\Filament\Resources\Scenarios;

use App\BI\Analysis\Infrastructure\Persistence\ScenarioModel;
use App\Filament\Resources\Scenarios\Pages\CreateScenario;
use App\Filament\Resources\Scenarios\Pages\EditScenario;
use App\Filament\Resources\Scenarios\Pages\ListScenarios;
use App\Filament\Resources\Scenarios\Schemas\ScenarioForm;
use App\Filament\Resources\Scenarios\Tables\ScenariosTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ScenarioResource extends Resource
{
    protected static ?string $model = ScenarioModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBeaker;

    protected static ?string $navigationLabel = 'Scénarios';

    protected static string|UnitEnum|null $navigationGroup = 'Analyses';

    protected static ?string $modelLabel = 'Scénario';

    protected static ?string $pluralModelLabel = 'Scénarios';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ScenarioForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ScenariosTable::configure($table);
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
            'index' => ListScenarios::route('/'),
            'create' => CreateScenario::route('/create'),
            'edit' => EditScenario::route('/{record}/edit'),
        ];
    }
}
