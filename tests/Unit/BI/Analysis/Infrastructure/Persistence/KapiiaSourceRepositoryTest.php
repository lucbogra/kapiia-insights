<?php

use App\BI\Analysis\Domain\ArchetypePopulationFilter;
use App\BI\Analysis\Infrastructure\Persistence\KapiiaSourceRepository;
use App\BI\Profiling\Domain\Criteria\DiscreteCriterion;
use App\BI\Profiling\Domain\Criteria\RangeCriterion;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class);

// ---------------------------------------------------------------------------
// Bootstrap : crée la table usagers_view en SQLite in-memory
// ---------------------------------------------------------------------------

beforeEach(function (): void {
    DB::statement('CREATE TABLE IF NOT EXISTS usagers_view (
        id     INTEGER PRIMARY KEY,
        age    INTEGER NOT NULL,
        genre  TEXT    NOT NULL
    )');

    DB::statement('DELETE FROM usagers_view');

    DB::table('usagers_view')->insert([
        ['id' => 1, 'age' => 20, 'genre' => 'H'],
        ['id' => 2, 'age' => 35, 'genre' => 'F'],
        ['id' => 3, 'age' => 50, 'genre' => 'H'],
    ]);
});

afterEach(function (): void {
    DB::statement('DROP TABLE IF EXISTS usagers_view');
});

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function makeRepo(): KapiiaSourceRepository
{
    return new KapiiaSourceRepository(
        connection: DB::connection(),
        sourceLabel: 'test',
    );
}

function makeFilter(array $criteria): ArchetypePopulationFilter
{
    return new ArchetypePopulationFilter($criteria);
}

// ---------------------------------------------------------------------------
// Tests
// ---------------------------------------------------------------------------

it('compte les usagers dans une plage complète (min ET max)', function (): void {
    $filter = makeFilter([
        new RangeCriterion(column: 'age', min: 20, max: 35),
    ]);

    expect(makeRepo()->countPopulation($filter))->toBe(2); // âges 20 et 35
});

it('compte les usagers avec seulement une borne min (plage ouverte à droite)', function (): void {
    $filter = makeFilter([
        new RangeCriterion(column: 'age', min: 35, max: null),
    ]);

    expect(makeRepo()->countPopulation($filter))->toBe(2); // âges 35 et 50
});

it('compte les usagers avec seulement une borne max (plage ouverte à gauche)', function (): void {
    $filter = makeFilter([
        new RangeCriterion(column: 'age', min: null, max: 35),
    ]);

    expect(makeRepo()->countPopulation($filter))->toBe(2); // âges 20 et 35
});

it('combine un critère discret et une plage partielle correctement', function (): void {
    $filter = makeFilter([
        new RangeCriterion(column: 'age', min: 30, max: null),
        new DiscreteCriterion(column: 'genre', value: 'H'),
    ]);

    expect(makeRepo()->countPopulation($filter))->toBe(1); // seul usager 3 (âge 50, genre H)
});

it('ignore un critère de plage sans bornes (cas défensif)', function (): void {
    $filter = makeFilter([
        new RangeCriterion(column: 'age', min: null, max: null),
    ]);

    expect(makeRepo()->countPopulation($filter))->toBe(3); // aucune contrainte appliquée
});
