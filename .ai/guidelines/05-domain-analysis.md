# Analysis Domain — Scenarios, Indicators, Precisions

The Analysis context extends beyond archetype analysis with a user-facing configuration layer.

## Three concepts, three registries

### Indicators — metrics to compute

Each indicator implements `IndicatorInterface` with `compute(array $dataset, array $parameters): mixed`.

- Code classes in `Domain/Indicator/`
- Registered in `IndicatorRegistry` (singleton in `BIServiceProvider`)
- Mirrored in DB via `IndicatorDefinition` (table `indicator_definitions`)
- Linked by `key` (string identifier)
- Seeded via `IndicatorDefinitionSeeder`

Target determined by `IndicatorTargetEnum`: `Incidents`, `Transmissions`, or `Global`.
Output determined by `IndicatorOutputTypeEnum`: `Scalar`, `ListItems`, `Distribution`, `GroupedAverage`.

### Precisions — SQL filters

Each precision implements `PrecisionInterface` with `apply(Builder $query, array $parameters): void`.

- Same Registry + Definition + Seeder pattern as Indicators
- `PrecisionTypeEnum`: `PopulationFilter` (restricts which usagers) or `DatasetFilter` (restricts which rows)

### Scenarios — user configurations

A Scenario combines:
- An archetype OR custom criteria values (never both — XOR rule)
- A list of precisions with their parameters
- A list of indicators with their parameters
- A set of source connections
- Owner + shared toggle

## Important rules

**XOR archetype/criteria** is enforced by `App\BI\Analysis\Domain\Service\PopulationSourceResolver`. Both `CreateScenarioUseCase` and `UpdateScenarioUseCase` inject and call it. Never duplicate this logic.

**Admin UI for Definitions is edit-only.** `IndicatorDefinitionResource` and `PrecisionDefinitionResource` do NOT allow creation or deletion via the admin panel. New definitions require:

1. Create the PHP class implementing the interface
2. Register it in `BIServiceProvider` (Registry binding)
3. Run the corresponding seeder

**Scenario visibility**: `ScenarioRepository::findVisibleTo(userId)` returns owned + shared scenarios. Filter the table query in `ScenarioResource::table()` with `modifyQueryUsing()`.

## Adding a new Indicator

1. Create `app/BI/Analysis/Domain/Indicator/MyNewIndicator.php` implementing `IndicatorInterface`
2. Add `$registry->register(new MyNewIndicator())` in `BIServiceProvider::register()`
3. Run `php artisan db:seed --class=IndicatorDefinitionSeeder`
4. If the output type is new, add a case to `IndicatorOutputTypeEnum` AND a matching `@case` in the blade view of the scenario run page

## Adding a new Precision

Same steps as Indicator, but with `PrecisionInterface` and `apply()` instead of `compute()`. If the precision is a `PopulationFilter`, make sure it generates a subquery with `whereIn('usagers_view.id', ...)` rather than a simple WHERE — this preserves the user filtering semantics.

## Run flow

`RunScenarioUseCase` orchestrates:

1. Load scenario and check visibility
2. Resolve population filter via `ScenarioPopulationFilterBuilder`
3. Resolve active precisions via Registry lookup
4. Resolve active indicators via Registry lookup
5. `ScenarioQueryOrchestrator` queries each source, applying precisions at SQL level
6. `ScenarioIndicatorCalculator` computes each selected indicator on its target dataset
7. Returns a `ScenarioResult`