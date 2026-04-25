<?php

namespace App\Filament\Pages;

use App\BI\Analysis\Application\UseCase\RunScenario\RunScenarioCommand;
use App\BI\Analysis\Application\UseCase\RunScenario\RunScenarioUseCase;
use App\BI\Analysis\Infrastructure\Persistence\ScenarioModel;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class RunScenario extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPlayCircle;

    protected static ?string $navigationLabel = 'Exécuter un scénario';

    protected static string|UnitEnum|null $navigationGroup = 'Analyses';

    protected static ?string $title = 'Exécution de scénario';

    protected static ?int $navigationSort = 2;

    // Masquer la page du menu — elle ne s'ouvre qu'en arrivant depuis un scénario
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'run-scenario';

    protected string $view = 'filament.pages.run-scenario';

    // --- État ---
    public ScenarioModel $scenario;

    public ?array $result = null;

    public bool $isRunning = false;

    public function mount(): void
    {
        $model = ScenarioModel::with(['precisions', 'indicators', 'sourceConnections', 'archetype', 'owner'])
            ->find(request('scenario'));

        if (! $model) {
            abort(404);
        }

        // Vérification de visibilité au niveau UI
        if ($model->owner_id !== Auth::id() && ! $model->is_shared) {
            abort(403);
        }

        $this->scenario = $model;
    }

    public function run(): void
    {
        $this->isRunning = true;

        try {
            $useCase = app(RunScenarioUseCase::class);

            $scenarioResult = $useCase->execute(new RunScenarioCommand(
                scenarioId: $this->scenario->id,
                requesterId: (string) Auth::id(),
            ));

            $this->result = [
                'population_count' => $scenarioResult->populationCount,
                'incident_count' => $scenarioResult->incidentCount,
                'indicator_results' => $scenarioResult->indicatorResults,
                'source_labels' => $scenarioResult->sourceLabels,
                'computed_at' => $scenarioResult->computedAt,
            ];

            Notification::make()
                ->title('Scénario exécuté avec succès')
                ->success()
                ->send();

        } catch (\DomainException $e) {
            Notification::make()
                ->title('Erreur d\'exécution')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Erreur technique')
                ->body($e->getMessage())
                ->danger()
                ->send();
        } finally {
            $this->isRunning = false;
        }
    }

    // --- Helpers pour la vue ---

    public function getScenarioSummary(): array
    {
        return [
            'name' => $this->scenario->name,
            'description' => $this->scenario->description,
            'owner' => $this->scenario->owner->name ?? '—',
            'is_shared' => $this->scenario->is_shared,
            'population' => $this->scenario->archetype
                                    ? 'Archétype : '.$this->scenario->archetype->nomenclature
                                    : 'Critères personnalisés',
            'precisions' => $this->scenario->precisions->count(),
            'indicators' => $this->scenario->indicators->count(),
            'sources' => $this->scenario->sourceConnections->pluck('label')->all(),
        ];
    }

    /**
     * Constantes d'output_type exposées à la vue Blade.
     * Évite d'importer l'enum directement dans Blade.
     */
    public function outputType(string $type): string
    {
        return $type;
    }
}
