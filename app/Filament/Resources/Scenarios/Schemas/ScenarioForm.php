<?php

namespace App\Filament\Resources\Scenarios\Schemas;

use App\BI\Analysis\Infrastructure\Persistence\IndicatorDefinitionModel;
use App\BI\Analysis\Infrastructure\Persistence\PrecisionDefinitionModel;
use App\BI\Profiling\Infrastructure\Persistence\ArchetypeCriterionModel;
use App\BI\Profiling\Infrastructure\Persistence\ArchetypeModel;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ScenarioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->schema([
            self::metadataSection()->columnSpanFull(),
            self::populationSection()->columnSpanFull(),
            self::precisionsSection(),
            self::indicatorsSection(),
            self::sourcesSection()->columnSpanFull(),
        ]);
    }

    // section métadonnées
    private static function metadataSection(): Section
    {
        return Section::make('Informations générales')
            ->schema([
                TextInput::make('name')
                    ->label('Nom du scénario')
                    ->required()
                    ->maxLength(150)
                    ->columnSpanFull(),

                Textarea::make('description')
                    ->label('Description')
                    ->rows(2)
                    ->columnSpanFull(),

                Toggle::make('is_shared')
                    ->label('Partager avec les autres analystes')
                    ->helperText('Si désactivé, seul vous pourrez voir et exécuter ce scénario.')
                    ->default(false),

                Hidden::make('owner_id')
                    ->default(fn () => Auth::id()),
            ])
            ->columns(1);
    }

    // section population (archétype ou critères custom)
    // C'est le plus subtil parce que les deux cas s'excluent mutuellement. On utilise un Radio pour choisir le mode, puis on affiche l'un ou l'autre via visible().
    private static function populationSection(): Section
    {
        return Section::make('Population analysée')
            ->description('Choisissez un archétype prédéfini ou définissez vos propres critères.')
            ->schema([

                Radio::make('population_mode')
                    ->label('Mode de sélection')
                    ->options([
                        'archetype' => 'Utiliser un archétype existant',
                        'custom' => 'Définir mes propres critères',
                    ])
                    ->default('archetype')
                    ->required()
                    ->live()
                    // ->dehydrated(false)   // ce champ ne va pas en BD
                    ->columnSpanFull(),

                // --- MODE ARCHÉTYPE ---
                Select::make('archetype_id')
                    ->label('Archétype')
                    ->options(
                        ArchetypeModel::where('is_active', true)
                            ->orderBy('nomenclature')
                            ->pluck('nomenclature', 'id')
                    )
                    ->searchable()
                    ->visible(fn (Get $get) => $get('population_mode') === 'archetype')
                    ->requiredIf('population_mode', 'archetype')
                    ->columnSpanFull(),

                // --- MODE CRITÈRES CUSTOM ---
                Group::make(self::buildCriteriaFields())
                    ->visible(fn (Get $get) => $get('population_mode') === 'custom')
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }

    /**
     * Génère dynamiquement les champs pour chaque critère actif en base.
     * Même logique que dans ArchetypeResource.
     */
    private static function buildCriteriaFields(): array
    {
        $criteria = ArchetypeCriterionModel::orderBy('sort_order')->get();

        return $criteria->map(function ($criterion) {
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
                        ->mapWithKeys(fn ($v) => [$v['value'] => $v['value']])
                        ->all()
                )
                ->placeholder('— Non défini —');
        })->all();
    }

    // Chaque précision a son propre schéma de paramètres. On utilise un Repeater dont le contenu s'adapte à la précision sélectionnée.
    private static function precisionsSection(): Section
    {
        return Section::make('Précisions appliquées')
            ->description('Filtres supplémentaires pour affiner la population ou le dataset.')
            ->schema([

                Repeater::make('precisions')
                    ->label('')
                    // ->relationship('precisions')
                    ->schema([

                        Select::make('precision_definition_id')
                            ->label('Précision')
                            ->options(
                                PrecisionDefinitionModel::where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->pluck('label', 'id')
                            )
                            ->required()
                            ->live()
                            ->columnSpanFull(),

                        // Les paramètres s'affichent dynamiquement selon la précision choisie
                        Group::make()
                            ->schema(fn (Get $get) => self::buildPrecisionParameterFields($get('precision_definition_id')))
                            ->columnSpanFull(),

                        Hidden::make('sort_order')
                            ->default(0),
                    ])
                    ->addActionLabel('Ajouter une précision')
                    ->collapsible()
                    ->collapsed()
                    ->itemLabel(
                        fn (array $state): ?string => self::getPrecisionLabel($state['precision_definition_id'] ?? null)
                    )
                    ->defaultItems(0),
            ]);
    }

    /**
     * Construit les champs de paramètres pour une précision donnée.
     */
    private static function buildPrecisionParameterFields(?string $precisionDefinitionId): array
    {
        if (! $precisionDefinitionId) {
            return [];
        }

        $definition = PrecisionDefinitionModel::find($precisionDefinitionId);

        if (! $definition || empty($definition->parameters_schema)) {
            return [];
        }

        $fields = [];

        foreach ($definition->parameters_schema as $paramKey => $paramConfig) {
            $fields[] = self::buildParameterField("parameters.{$paramKey}", $paramConfig);
        }

        return $fields;
    }

    /**
     * Construit un champ Filament à partir d'un schéma de paramètre.
     * Utilisé aussi pour les indicateurs.
     */
    private static function buildParameterField(string $statePath, array $config): Component
    {
        $label = $config['label'] ?? $statePath;
        $default = $config['default'] ?? null;

        return match ($config['type']) {
            'int' => TextInput::make($statePath)
                ->label($label)
                ->numeric()
                ->required()
                ->default($default)
                ->minValue($config['min'] ?? null)
                ->maxValue($config['max'] ?? null),

            'date' => DatePicker::make($statePath)
                ->label($label)
                ->default($default),

            'string' => TextInput::make($statePath)
                ->label($label)
                ->default($default),

            'bool' => Toggle::make($statePath)
                ->label($label)
                ->default($default ?? false),

            default => TextInput::make($statePath)
                ->label($label)
                ->default($default),
        };
    }

    private static function getPrecisionLabel(?string $id): ?string
    {
        if (! $id) {
            return 'Nouvelle précision';
        }

        return PrecisionDefinitionModel::find($id)?->label;
    }

    // section indicateurs (même pattern que les précisions)
    private static function indicatorsSection(): Section
    {
        return Section::make('Indicateurs à mesurer')
            ->description('Sélectionnez les mesures à calculer lors de l\'exécution.')
            ->schema([

                Repeater::make('indicators')
                    ->label('Indicateurs')
                    // ->relationship('indicators')
                    ->schema([

                        Select::make('indicator_definition_id')
                            ->label('Indicateur')
                            ->options(
                                IndicatorDefinitionModel::where('is_active', true)
                                    ->orderBy('sort_order')
                                    ->pluck('label', 'id')
                            )
                            ->required()
                            ->live()
                            ->columnSpanFull(),

                        Group::make()
                            ->schema(fn (Get $get) => self::buildIndicatorParameterFields($get('indicator_definition_id')))
                            ->columnSpanFull(),

                        Hidden::make('sort_order')
                            ->default(0),
                    ])
                    ->addActionLabel('Ajouter un indicateur')
                    ->collapsible()
                    ->collapsed()
                    ->itemLabel(
                        fn (array $state): ?string => self::getIndicatorLabel($state['indicator_definition_id'] ?? null)
                    )
                    ->minItems(1)
                    ->validationMessages([
                        'min' => 'Sélectionnez au moins un indicateur.',
                    ]),
            ]);
    }

    private static function buildIndicatorParameterFields(?string $indicatorDefinitionId): array
    {
        if (! $indicatorDefinitionId) {
            return [];
        }

        $definition = IndicatorDefinitionModel::find($indicatorDefinitionId);

        if (! $definition || empty($definition->parameters_schema)) {
            return [];
        }

        $fields = [];

        foreach ($definition->parameters_schema as $paramKey => $paramConfig) {
            $fields[] = self::buildParameterField("parameters.{$paramKey}", $paramConfig);
        }

        return $fields;
    }

    private static function getIndicatorLabel(?string $id): ?string
    {
        if (! $id) {
            return 'Nouvel indicateur';
        }

        return IndicatorDefinitionModel::find($id)?->label;
    }

    // section sources
    private static function sourcesSection(): Section
    {
        return Section::make('Sources de données interrogées')
            ->schema([

                CheckboxList::make('sourceConnections')
                    ->label('')
                    ->relationship('sourceConnections', 'label',
                        modifyQueryUsing: fn ($query) => $query->where('is_active', true)
                    )
                    ->columns(2)
                    ->required()
                    ->validationMessages([
                        'required' => 'Sélectionnez au moins une source.',
                    ]),
            ]);
    }
}
