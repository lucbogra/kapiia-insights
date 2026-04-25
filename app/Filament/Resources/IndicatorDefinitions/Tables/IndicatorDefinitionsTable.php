<?php

namespace App\Filament\Resources\IndicatorDefinitions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IndicatorDefinitionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                // TextColumn::make('key')
                //     ->label('Clé')
                //     ->fontFamily('mono')
                //     ->searchable(),

                TextColumn::make('label')
                    ->label('Libellé')
                    ->searchable(),

                TextColumn::make('target')
                    ->badge()
                    ->label('Cible')
                    ->colors([
                        'info' => 'incidents',
                        'warning' => 'transmissions',
                        'gray' => 'global',
                    ]),

                TextColumn::make('output_type')
                    ->badge()
                    ->label('Sortie')
                    ->color('gray'),

                IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                ]),
            ]);
    }
}
