<?php

namespace App\Providers;

// Interfaces
use App\BI\Analysis\Domain\Repository\AnalysisResultRepositoryInterface;
use App\BI\Profiling\Domain\Repository\ArchetypeCriterionRepositoryInterface;
use App\BI\Profiling\Domain\Repository\ArchetypeRepositoryInterface;
use App\BI\DataSource\Domain\Repository\SourceConnectionRepositoryInterface;

// Repositories
use App\BI\DataSource\Infrastructure\Persistence\SourceConnectionRepository;
use App\BI\Profiling\Infrastructure\Persistence\ArchetypeCriterionRepository;
use App\BI\Profiling\Infrastructure\Persistence\ArchetypeRepository;
use App\BI\Analysis\Infrastructure\Persistence\AnalysisResultRepository;


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
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
