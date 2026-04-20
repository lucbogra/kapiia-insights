<?php

namespace App\Filament\Resources\ArchetypeCriteria;

use App\BI\Profiling\Infrastructure\Persistence\ArchetypeCriterionModel;
use App\Filament\Resources\ArchetypeCriteria\Pages\CreateArchetypeCriterion;
use App\Filament\Resources\ArchetypeCriteria\Pages\EditArchetypeCriterion;
use App\Filament\Resources\ArchetypeCriteria\Pages\ListArchetypeCriteria;
use App\Filament\Resources\ArchetypeCriteria\Schemas\ArchetypeCriterionForm;
use App\Filament\Resources\ArchetypeCriteria\Tables\ArchetypeCriteriaTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ArchetypeCriterionResource extends Resource
{
    protected static ?string $model = ArchetypeCriterionModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static ?string $navigationLabel = 'Critères';
    protected static ?string $modelLabel = 'Critère';
    protected static ?string $pluralModelLabel = 'Critères';
    protected static ?int $navigationSort = 2;


    public static function form(Schema $schema): Schema
    {
        return ArchetypeCriterionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArchetypeCriteriaTable::configure($table);
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
            'index' => ListArchetypeCriteria::route('/'),
            'create' => CreateArchetypeCriterion::route('/create'),
            'edit' => EditArchetypeCriterion::route('/{record}/edit'),
        ];
    }
}
