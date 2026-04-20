<?php

namespace App\Filament\Resources\ArchetypeCriteria\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ArchetypeCriteriaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),

                TextColumn::make('key')
                    ->label('Clé')
                    ->searchable()
                    ->fontFamily('mono'),

                TextColumn::make('label')
                    ->label('Libellé')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->colors([
                        'info'    => 'discrete',
                        'warning' => 'range',
                    ]),

                TextColumn::make('source_column')
                    ->label('Colonne source')
                    ->fontFamily('mono'),

                TextColumn::make('nomenclature_prefix')
                    ->label('Préfixe')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
