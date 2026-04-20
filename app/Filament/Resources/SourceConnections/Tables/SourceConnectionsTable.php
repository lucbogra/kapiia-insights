<?php

namespace App\Filament\Resources\SourceConnections\Tables;

use App\BI\DataSource\Infrastructure\Persistence\SourceConnectionModel;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SourceConnectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('host')
                    ->label('Hôte')
                    ->searchable(),

                TextColumn::make('port')
                    ->label('Port'),

                TextColumn::make('driver')
                    ->label('Driver')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'mysql' => 'info',
                        'pgsql' => 'warning',
                        default => 'gray',
                    }),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('last_tested_at')
                    ->label('Dernier test')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Jamais testé')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('test')
                    ->label('Tester')
                    ->icon('heroicon-o-signal')
                    ->color('info')
                    ->action(function (SourceConnectionModel $record, $livewire) {
                        // On passe par le service applicatif
                        $service = app(
                            \App\BI\DataSource\Application\SourceConnectionService::class
                        );

                        $success = $service->test($record->id);

                        if ($success) {
                            \Filament\Notifications\Notification::make()
                                ->title('Connexion réussie')
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Connexion échouée')
                                ->danger()
                                ->send();
                        }
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
