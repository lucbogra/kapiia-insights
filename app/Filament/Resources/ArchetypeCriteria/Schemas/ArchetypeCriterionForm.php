<?php

namespace App\Filament\Resources\ArchetypeCriteria\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class ArchetypeCriterionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Définition du critère')
                ->schema([
                    TextInput::make('key')
                        ->label('Clé machine')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50)
                        ->helperText('Ex: birth_year, sex, sibling_count'),

                    TextInput::make('label')
                        ->label('Libellé')
                        ->required()
                        ->maxLength(100),

                    Select::make('type')
                        ->label('Type')
                        ->options([
                            'discrete' => 'Discret (valeur fixe)',
                            'range'    => 'Plage (min → max)',
                        ])
                        ->required()
                        ->live(),

                    TextInput::make('source_column')
                        ->label('Colonne source')
                        ->required()
                        ->maxLength(80)
                        ->helperText('Nom de la colonne dans les BDs Kapiia'),

                    TextInput::make('nomenclature_prefix')
                        ->label('Préfixe nomenclature')
                        ->maxLength(10)
                        ->helperText('Ex: M, SEP, ORP'),

                    TextInput::make('sort_order')
                        ->label('Ordre')
                        ->numeric()
                        ->default(0),
                ])
                ->columns(2),

            // Options dynamiques selon le type
            Section::make('Options')
                ->schema([
                    // Discret : liste de valeurs
                    Repeater::make('options.values')
                        ->label('Valeurs possibles')
                        ->schema([
                            TextInput::make('value')
                                ->label('Valeur')
                                ->required(),
                        ])
                        ->visible(fn(Get $get) => $get('type') === 'discrete')
                        ->addActionLabel('Ajouter une valeur')
                        ->columnSpanFull(),

                    // Range : min, max, step
                    TextInput::make('options.min')
                        ->label('Valeur minimum')
                        ->numeric()
                        ->default(0)
                        ->visible(fn(Get $get) => $get('type') === 'range'),

                    TextInput::make('options.max')
                        ->label('Valeur maximum')
                        ->numeric()
                        ->default(100)
                        ->visible(fn(Get $get) => $get('type') === 'range'),

                    TextInput::make('options.step')
                        ->label('Pas')
                        ->numeric()
                        ->default(1)
                        ->visible(fn(Get $get) => $get('type') === 'range'),
                ]),
        ]);
    }
}
