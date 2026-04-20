<?php

namespace App\Filament\Resources\Archetypes\Tables;

use App\BI\Profiling\Infrastructure\Persistence\ArchetypeModel;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ArchetypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomenclature')
                    ->label('Nomenclature')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->weight('bold'),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->placeholder('—'),

                IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),

                TextColumn::make('sourceConnections.label')
                    ->label('Sources')
                    ->badge()
                    ->color('teal')
                    ->separator(','),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('analyze')
                    ->label('Analyser')
                    ->icon('heroicon-o-chart-bar')
                    ->color('success')
                    ->action(function (ArchetypeModel $record) {
                        $useCase = app(
                            \App\BI\Analysis\Application\UseCase\RunAnalysis\RunAnalysisUseCase::class
                        );

                        $sourceIds = $record->sourceConnections->pluck('id')->all();

                        $useCase->execute(
                            new \App\BI\Analysis\Application\UseCase\RunAnalysis\RunAnalysisCommand(
                                archetypeId:         $record->id,
                                sourceConnectionIds: $sourceIds,
                            )
                        );

                        \Filament\Notifications\Notification::make()
                            ->title('Analyse lancée avec succès')
                            ->success()
                            ->send();
                    }),

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
