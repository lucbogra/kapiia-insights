<?php

namespace App\Filament\Resources\Archetypes;

use App\BI\Profiling\Infrastructure\Persistence\ArchetypeModel;
use App\Filament\Resources\Archetypes\Pages\CreateArchetype;
use App\Filament\Resources\Archetypes\Pages\EditArchetype;
use App\Filament\Resources\Archetypes\Pages\ListArchetypes;
use App\Filament\Resources\Archetypes\Schemas\ArchetypeForm;
use App\Filament\Resources\Archetypes\Tables\ArchetypesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ArchetypeResource extends Resource
{
    protected static ?string $model = ArchetypeModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static ?string $navigationLabel = 'Archétypes';
    protected static ?string $modelLabel = 'Archétype';
    protected static ?string $pluralModelLabel = 'Archétypes';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return ArchetypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ArchetypesTable::configure($table);
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
            'index' => ListArchetypes::route('/'),
            'create' => CreateArchetype::route('/create'),
            'edit' => EditArchetype::route('/{record}/edit'),
        ];
    }
}
