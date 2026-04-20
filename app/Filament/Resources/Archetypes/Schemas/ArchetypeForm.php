<?php

namespace App\Filament\Resources\Archetypes\Schemas;

use App\BI\Profiling\Infrastructure\Persistence\ArchetypeCriterionModel;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ArchetypeForm
{
    public static function configure(Schema $schema): Schema
    {
        // Charger les critères triés pour construire le formulaire dynamiquement
        $criteria = ArchetypeCriterionModel::orderBy('sort_order')->get();

        $criteriaFields = $criteria->map(function ($criterion) {
            if ($criterion->type === 'range') {
                $bounds = $criterion->options ?? [];
                return Fieldset::make($criterion->label)
                    ->schema([
                        TextInput::make("criteria_values.{$criterion->key}.from")
                            ->label('De')
                            ->numeric()
                            ->minValue($bounds['min'] ?? 0)
                            ->maxValue($bounds['max'] ?? 100),

                        TextInput::make("criteria_values.{$criterion->key}.to")
                            ->label('À')
                            ->numeric()
                            ->minValue($bounds['min'] ?? 0)
                            ->maxValue($bounds['max'] ?? 100),
                    ])
                    ->columns(2);
            }

            return Select::make("criteria_values.{$criterion->key}")
                ->label($criterion->label)
                ->options(
                    collect($criterion->options['values'] ?? [])
                    ->mapWithKeys(fn($v, $key) => [$v['value'] => $v['value']])->all()
                )
                ->placeholder('— Non défini —');
        })->all();

        return $schema->schema([
            Section::make('Critères de l\'archétype')
                ->schema($criteriaFields),

            Section::make('Informations')
                ->schema([
                    TextInput::make('nomenclature')
                        ->label('Nomenclature')
                        ->disabled()
                        ->helperText('Générée automatiquement à la création'),

                    Textarea::make('description')
                        ->label('Description')
                        ->rows(3)
                        ->columnSpanFull(),

                    Toggle::make('is_active')
                        ->label('Actif')
                        ->default(true),
                ])
                ->columns(2),

            Section::make('Sources de données associées')
                ->schema([
                    CheckboxList::make('sourceConnections')
                        ->relationship('sourceConnections', 'label')
                        ->label('Sources')
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
