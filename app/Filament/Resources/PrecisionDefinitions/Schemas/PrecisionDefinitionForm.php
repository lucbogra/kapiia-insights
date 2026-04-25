<?php

namespace App\Filament\Resources\PrecisionDefinitions\Schemas;

use App\BI\Analysis\Domain\Precision\PrecisionRegistry;
use App\BI\Analysis\Domain\Precision\PrecisionTargetEnum;
use App\BI\Analysis\Domain\Precision\PrecisionTypeEnum;
use App\BI\Analysis\Infrastructure\Persistence\PrecisionDefinitionModel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PrecisionDefinitionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make()->schema([

                Select::make('key')
                    ->label('Précision (code)')
                    ->options(function () {
                        $registry = app(PrecisionRegistry::class);
                        $existingKeys = PrecisionDefinitionModel::pluck('key')->all();

                        return collect($registry->all())
                            ->filter(fn ($p) => ! in_array($p->key(), $existingKeys, true))
                            ->mapWithKeys(fn ($p) => [$p->key() => $p->label()])
                            ->all();
                    })
                    ->required()
                    ->disabledOn('edit'),

                TextInput::make('label')
                    ->label('Libellé')
                    ->required()
                    ->maxLength(150),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(2)
                    ->columnSpanFull(),

                Grid::make(3)->schema([
                    TextInput::make('type')
                        ->label('Type')
                        ->disabled()
                        ->dehydrated(false)
                        ->formatStateUsing(
                            fn ($state) => $state instanceof PrecisionTypeEnum
                                ? $state->label()
                                : $state
                        ),

                    TextInput::make('target')
                        ->label('Cible')
                        ->disabled()
                        ->dehydrated(false)
                        ->formatStateUsing(
                            fn ($state) => $state instanceof PrecisionTargetEnum
                                ? $state->label()
                                : $state
                        ),

                    TextInput::make('sort_order')
                        ->label('Ordre')
                        ->numeric()
                        ->default(0),
                ]),

                Toggle::make('is_active')
                    ->label('Actif')
                    ->default(true),
            ]),
        ]);
    }
}
