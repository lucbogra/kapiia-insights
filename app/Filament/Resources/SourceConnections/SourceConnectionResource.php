<?php

namespace App\Filament\Resources\SourceConnections;

use App\BI\DataSource\Infrastructure\Persistence\SourceConnectionModel;
use App\Filament\Resources\SourceConnections\Pages\CreateSourceConnection;
use App\Filament\Resources\SourceConnections\Pages\EditSourceConnection;
use App\Filament\Resources\SourceConnections\Pages\ListSourceConnections;
use App\Filament\Resources\SourceConnections\Schemas\SourceConnectionForm;
use App\Filament\Resources\SourceConnections\Tables\SourceConnectionsTable;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SourceConnectionResource extends Resource
{
    protected static ?string $model = SourceConnectionModel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCircleStack;
    protected static ?string $navigationLabel = 'Sources de données';
    protected static ?string $modelLabel = 'Source';
    protected static ?string $pluralModelLabel = 'Sources de données';
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informations de connexion')
                ->schema([
                    TextInput::make('label')
                        ->label('Nom')
                        ->required()
                        ->maxLength(100)
                        ->columnSpanFull(),

                    TextInput::make('host')
                        ->label('Hôte')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('port')
                        ->label('Port')
                        ->numeric()
                        ->required()
                        ->default(3306),

                    Select::make('driver')
                        ->label('Driver')
                        ->options([
                            'mysql' => 'MySQL',
                            'pgsql' => 'PostgreSQL',
                        ])
                        ->required()
                        ->default('mysql'),

                    TextInput::make('database_name')
                        ->label('Base de données')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('username')
                        ->label('Utilisateur')
                        ->required()
                        ->maxLength(100),

                    TextInput::make('password')
                        ->label('Mot de passe')
                        ->password()
                        ->revealable()
                        ->required()
                        ->maxLength(255)
                        ->dehydrateStateUsing(fn($state) => filled($state) ? $state : null)
                        ->dehydrated(fn($state) => filled($state)),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true)
                        ->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return SourceConnectionsTable::configure($table);
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
            'index' => ListSourceConnections::route('/'),
            'create' => CreateSourceConnection::route('/create'),
            'edit' => EditSourceConnection::route('/{record}/edit'),
        ];
    }
}
