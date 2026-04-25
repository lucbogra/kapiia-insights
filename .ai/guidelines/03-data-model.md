# Data Model Quirks

## ULIDs

**Always lowercase.** All models use the `HasLowercaseUlids` trait located at `app/BI/Shared/Infrastructure/Persistence/HasLowercaseUlids.php`. It:

- Overrides `newUniqueId()` to force lowercase generation
- Overrides `resolveRouteBindingQuery()` to normalize incoming URL params

Never generate ULIDs with raw `Str::ulid()` outside entity construction — always go through model creation or the trait.

## Pivot tables

Never use composite primary keys (`$table->primary(['a_id', 'b_id'])`) — Laravel/Eloquent handles them poorly. Instead:

```php
$table->ulid('id')->primary();
$table->ulid('archetype_id');
$table->ulid('source_connection_id');
// ...
$table->unique(['archetype_id', 'source_connection_id']);
```

## Password encryption

`SourceConnectionModel` uses the `'encrypted'` cast for the `password` column. Never call `encrypt()` / `decrypt()` manually — the cast handles it transparently.

## Criteria values sanitization

User input for `criteria_values` (both Archetype and Scenario) contains empty fields that must be stripped before persistence.

Use `App\BI\Profiling\Domain\Service\CriteriaValuesSanitizer::sanitize()` in every Use Case that accepts criteria values. It removes:

- `null` or empty string values
- Fully-empty ranges (`{from: null, to: null}`)
- `null` items inside ranges (keeps partial ranges like `{from: 2010}`)

## Kapiia source database conventions

External Kapiia databases use French column names:

- Main user table is `usagers_view` (NOT `users`)
- `incidents` table columns:
  - `usager_id` (FK)
  - `date_incident` (datetime)
  - `lieu` (string)
  - `incident_intitule_id` (FK to reference table)
  - `gravite` (tinyint 1-5)
  - `incident_attitude_id` (FK to reference table)
- Always use French field names in DTOs targeting Kapiia data (`gravite`, `intitule`, `lieu`, `dateIncident`, `attitude`)

## JSON fields to always cast

Any `json` column must be cast to `'array'` in the Eloquent model:

```php
protected $casts = [
    'criteria_values'    => 'array',
    'parameters_schema'  => 'array',
    'parameters'         => 'array',
];
```

Enum-backed columns must cast to the enum class:

```php
'target'      => IndicatorTargetEnum::class,
'output_type' => IndicatorOutputTypeEnum::class,
```