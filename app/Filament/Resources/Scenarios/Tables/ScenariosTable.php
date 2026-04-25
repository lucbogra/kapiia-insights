<?php

namespace App\Filament\Resources\Scenarios\Tables;

use App\BI\Analysis\Infrastructure\Persistence\ScenarioModel;
use App\Filament\Pages\RunScenario;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ScenariosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('owner.name')
                    ->label('Propriétaire')
                    ->sortable(),

                IconColumn::make('is_shared')
                    ->label('Partagé')
                    ->boolean(),

                TextColumn::make('archetype.nomenclature')
                    ->label('Archétype')
                    ->placeholder('— critères custom —')
                    ->fontFamily('mono'),

                TextColumn::make('updated_at')
                    ->label('Modifié le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_shared')
                    ->label('Visibilité')
                    ->trueLabel('Partagés uniquement')
                    ->falseLabel('Privés uniquement'),
            ])
            ->recordActions([
                Action::make('run')
                    ->label('Exécuter')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->url(fn (ScenarioModel $record) => RunScenario::getUrl(['scenario' => $record->id])),

                EditAction::make(),
                DeleteAction::make(),
            ])
            ->modifyQueryUsing(fn ($query) => $query->where(function ($q) {
                $q->where('owner_id', Auth::id())
                    ->orWhere('is_shared', true);
            }));
    }
}
