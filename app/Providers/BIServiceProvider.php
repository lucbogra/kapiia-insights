<?php

namespace App\Providers;

// Interfaces
use App\BI\Analysis\Domain\Indicator\AverageBehaviorByActivityIndicator;
use App\BI\Analysis\Domain\Indicator\AverageGraviteIndicator;
use App\BI\Analysis\Domain\Indicator\GraviteDistributionIndicator;
use App\BI\Analysis\Domain\Indicator\IncidentCountIndicator;
use App\BI\Analysis\Domain\Indicator\IndicatorRegistry;
use App\BI\Analysis\Domain\Indicator\TopIncidentTitlesIndicator;
use App\BI\Analysis\Domain\Precision\IncidentDateRangePrecision;
// Repositories
use App\BI\Analysis\Domain\Precision\MinGraviteThresholdPrecision;
use App\BI\Analysis\Domain\Precision\MinIncidentCountPrecision;
use App\BI\Analysis\Domain\Precision\PrecisionRegistry;
use App\BI\Analysis\Domain\Repository\AnalysisResultRepositoryInterface;
use App\BI\Analysis\Domain\Repository\IndicatorDefinitionRepositoryInterface;
use App\BI\Analysis\Domain\Repository\PrecisionDefinitionRepositoryInterface;
use App\BI\Analysis\Domain\Repository\ScenarioRepositoryInterface;
use App\BI\Analysis\Infrastructure\Persistence\AnalysisResultRepository;
use App\BI\Analysis\Infrastructure\Persistence\IndicatorDefinitionRepository;
use App\BI\Analysis\Infrastructure\Persistence\PrecisionDefinitionRepository;
use App\BI\Analysis\Infrastructure\Persistence\ScenarioRepository;
use App\BI\DataSource\Domain\Repository\SourceConnectionRepositoryInterface;
use App\BI\DataSource\Infrastructure\Persistence\SourceConnectionRepository;
use App\BI\Profiling\Domain\Repository\ArchetypeCriterionRepositoryInterface;
use App\BI\Profiling\Domain\Repository\ArchetypeRepositoryInterface;
use App\BI\Profiling\Infrastructure\Persistence\ArchetypeCriterionRepository;
use App\BI\Profiling\Infrastructure\Persistence\ArchetypeRepository;
use Illuminate\Support\ServiceProvider;

class BIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            SourceConnectionRepositoryInterface::class,
            SourceConnectionRepository::class,
        );

        $this->app->bind(
            ArchetypeRepositoryInterface::class,
            ArchetypeRepository::class,
        );

        $this->app->bind(
            ArchetypeCriterionRepositoryInterface::class,
            ArchetypeCriterionRepository::class,
        );

        $this->app->bind(
            AnalysisResultRepositoryInterface::class,
            AnalysisResultRepository::class,
        );

        $this->app->bind(
            IndicatorDefinitionRepositoryInterface::class,
            IndicatorDefinitionRepository::class,
        );

        $this->app->singleton(IndicatorRegistry::class, function () {
            $registry = new IndicatorRegistry;
            $registry->register(new IncidentCountIndicator);
            $registry->register(new AverageGraviteIndicator);
            $registry->register(new TopIncidentTitlesIndicator);
            $registry->register(new GraviteDistributionIndicator);
            // $registry->register(new AverageBehaviorByActivityIndicator);

            return $registry;
        });

        $this->app->bind(
            PrecisionDefinitionRepositoryInterface::class,
            PrecisionDefinitionRepository::class,
        );

        $this->app->singleton(PrecisionRegistry::class, function () {
            $registry = new PrecisionRegistry;
            $registry->register(new MinIncidentCountPrecision);
            $registry->register(new MinGraviteThresholdPrecision);
            $registry->register(new IncidentDateRangePrecision);

            return $registry;
        });

        $this->app->bind(
            ScenarioRepositoryInterface::class,
            ScenarioRepository::class,
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
