<?php

namespace App\Filament\Resources\IndicatorDefinitions\Schemas;

use App\BI\Analysis\Domain\Indicator\IndicatorOutputTypeEnum;
use App\BI\Analysis\Domain\Indicator\IndicatorRegistry;
use App\BI\Analysis\Domain\Indicator\IndicatorTargetEnum;
use App\BI\Analysis\Infrastructure\Persistence\IndicatorDefinitionModel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IndicatorDefinitionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make()->schema([

                Select::make('key')
                    ->label('Indicateur (code)')
                    ->options(function () {
                        $registry = app(IndicatorRegistry::class);
                        $existingKeys = IndicatorDefinitionModel::pluck('key')->all();

                        return collect($registry->all())
                            ->filter(fn ($i) => ! in_array($i->key(), $existingKeys, true))
                            ->mapWithKeys(fn ($i) => [$i->key() => $i->label()])
                            ->all();
                    })
                    ->required()
                    ->disabledOn('edit')
                    ->helperText('Seuls les indicateurs non encore enregistrés sont listés'),

                TextInput::make('label')
                    ->label('Libellé')
                    ->required()
                    ->maxLength(150),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(2)
                    ->columnSpanFull(),

                Grid::make(3)->schema([
                    Select::make('target')
                        ->label('Cible')
                        ->options(
                            collect(IndicatorTargetEnum::cases())
                                ->mapWithKeys(fn ($t) => [$t->value => $t->label()])
                                ->all()
                        )
                        ->disabled()
                        ->dehydrated(false),
                    Select::make('output_type')
                        ->label('Type de sortie')
                        ->options(
                            collect(IndicatorOutputTypeEnum::cases())
                                ->mapWithKeys(fn ($o) => [$o->value => $o->label()])
                                ->all()
                        )
                        ->disabled()
                        ->dehydrated(false),
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
