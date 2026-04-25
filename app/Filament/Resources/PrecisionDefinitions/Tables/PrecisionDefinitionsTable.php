<?php

namespace App\Filament\Resources\PrecisionDefinitions\Tables;

use App\BI\Analysis\Domain\Precision\PrecisionTargetEnum;
use App\BI\Analysis\Domain\Precision\PrecisionTypeEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PrecisionDefinitionsTable
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
                    ->searchable()
                    ->wrap(),

                TextColumn::make('type')
                    ->badge()
                    ->label('Type')
                    ->formatStateUsing(fn (PrecisionTypeEnum $state) => $state->label())
                    ->colors([
                        'warning' => fn ($state) => $state === PrecisionTypeEnum::PopulationFilter,
                        'info' => fn ($state) => $state === PrecisionTypeEnum::DatasetFilter,
                    ]),

                TextColumn::make('target')
                    ->badge()
                    ->label('Cible')
                    ->formatStateUsing(fn (PrecisionTargetEnum $state) => $state->label())
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
