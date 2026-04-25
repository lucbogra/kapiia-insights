<?php

namespace App\Filament\Pages;

use App\BI\Analysis\Application\UseCase\InvalidateCache\InvalidateCacheUseCase;
use App\BI\Analysis\Application\UseCase\RunAnalysis\RunAnalysisCommand;
use App\BI\Analysis\Application\UseCase\RunAnalysis\RunAnalysisUseCase;
use App\BI\DataSource\Infrastructure\Persistence\SourceConnectionModel;
use App\BI\Profiling\Infrastructure\Persistence\ArchetypeModel;
use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class AnalysisDashboard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Tableau d\'analyse';

    protected static string|UnitEnum|null $navigationGroup = 'Analyses';

    protected static ?string $title = 'Tableau d\'analyse';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.analysis-dashboard';

    // --- État du formulaire ---
    public ?string $archetype_id = null;

    public array $source_connection_ids = [];

    public int $cache_ttl_hours = 24;

    // --- Résultats ---
    public ?array $result = null;

    public bool $fromCache = false;

    public bool $isLoading = false;

    public ?string $cacheId = null;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Paramètres de l\'analyse')
                    ->schema([
                        Select::make('archetype_id')
                            ->label('Archétype')
                            ->options(
                                ArchetypeModel::where('is_active', true)
                                    ->orderBy('nomenclature')
                                    ->pluck('nomenclature', 'id')
                            )
                            ->searchable()
                            ->required()
                            ->placeholder('Sélectionner un archétype'),

                        CheckboxList::make('source_connection_ids')
                            ->label('Sources de données')
                            ->options(
                                SourceConnectionModel::where('is_active', true)
                                    ->orderBy('label')
                                    ->pluck('label', 'id')
                            )
                            ->columns(2)
                            ->required(),

                        Select::make('cache_ttl_hours')
                            ->label('Durée du cache')
                            ->options([
                                1 => '1 heure',
                                6 => '6 heures',
                                24 => '24 heures',
                                72 => '3 jours',
                            ])
                            ->default(24),
                    ])
                    ->columns(1),
            ]);
    }

    public function runAnalysis(): void
    {
        $data = $this->form->getState();

        if (empty($data['archetype_id']) || empty($data['source_connection_ids'])) {
            Notification::make()
                ->title('Veuillez sélectionner un archétype et au moins une source.')
                ->warning()
                ->send();

            return;
        }

        try {
            $useCase = app(RunAnalysisUseCase::class);

            $analysisResult = $useCase->execute(new RunAnalysisCommand(
                archetypeId: $data['archetype_id'],
                sourceConnectionIds: $data['source_connection_ids'],
                cacheTtlHours: (int) $data['cache_ttl_hours'],
            ));

            $this->result = $analysisResult->payload;
            $this->cacheId = $analysisResult->id;

            $this->fromCache = $analysisResult->isFresh()
                && $analysisResult->populationCount > 0;

            Notification::make()
                ->title('Analyse terminée')
                ->success()
                ->send();

        } catch (\DomainException $e) {
            Notification::make()
                ->title($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function invalidateCache(): void
    {
        // dd($this->cacheId);
        if ($this->cacheId) {
            $useCase = app(InvalidateCacheUseCase::class);
            $useCase->execute($this->cacheId);

            $this->result = null;
            $this->fromCache = false;
            $this->cacheId = false;

            Notification::make()
                ->title('Cache invalidé')
                ->success()
                ->send();
        }

    }

    // --- Helpers pour la vue ---

    public function getPopulationCount(): int
    {
        return $this->result['population_count'] ?? 0;
    }

    public function getIncidentTotal(): int
    {
        return $this->result['incidents_total'] ?? 0;
    }

    public function getAverageGravite(): float
    {
        return $this->result['average_gravite'] ?? 0.0;
    }

    public function getIncidentsByTitle(): array
    {
        return $this->result['incidents_by_title'] ?? [];
    }

    public function getGraviteDistribution(): array
    {
        return $this->result['gravite_distribution'] ?? [];
    }

    public function getAvgBehaviorByActivity(): array
    {
        return $this->result['avg_behavior_by_activity'] ?? [];
    }

    public function getAvgConcentrationByActivity(): array
    {
        return $this->result['avg_concentration_by_activity'] ?? [];
    }

    public function getAvgSocialByActivity(): array
    {
        return $this->result['avg_social_by_activity'] ?? [];
    }

    public function getSources(): array
    {
        return $this->result['sources'] ?? [];
    }
}
