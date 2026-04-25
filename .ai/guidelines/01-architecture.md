# Architecture — Domain-Driven Design

This project follows DDD with three bounded contexts under `app/BI/`.

## Bounded contexts

- **DataSource** — connections to external Kapiia databases
- **Profiling** — archetypes and criteria definitions
- **Analysis** — scenarios, indicators, precisions, query execution

Dependency rule: `Analysis` depends on `Profiling` and `DataSource`, never the reverse. `DataSource` and `Profiling` do not know each other.

## Layered structure per context

Each context is organized in three layers:

{Context}/
├── Domain/           # Pure PHP, zero Laravel dependencies
├── Application/      # Use Cases, commands, DTOs, orchestration
└── Infrastructure/   # Eloquent models, repository implementations

## Layer rules

**Domain layer**
- Pure PHP only — no `use Illuminate\...`, no Eloquent, no facades
- Entities are `final` classes with `readonly` properties (immutable)
- Repository interfaces live here (`Domain/Repository/`)
- Domain services (pure business logic) live in `Domain/Service/`
- Value Objects (Criterion types, etc.) live in sub-folders

**Application layer**
- Use Cases orchestrate Domain objects and call repository interfaces
- One Use Case = one folder = one Command DTO + one UseCase class
- DTOs (data transport) live in `Application/DTO/`, never in `Domain/`

**Infrastructure layer**
- Eloquent models live in `Infrastructure/Persistence/`, suffixed `Model`
- Repository implementations live here and implement Domain interfaces
- The `toDomain()` private method maps Eloquent to Domain entities

## Dependency injection

- Repository interface → implementation bindings are in `app/Providers/BIServiceProvider.php`
- Registries (`IndicatorRegistry`, `PrecisionRegistry`) are registered as singletons there too
- Concrete services without interfaces (Sanitizer, Resolver) are auto-wired by Laravel

## Example of correct entity

```php
namespace App\BI\Profiling\Domain;

final class Archetype
{
    public function __construct(
        public readonly string $id,
        public readonly array  $criteriaValues,
        public readonly string $criteriaHash,
        public readonly string $nomenclature,
        public readonly bool   $isActive,
    ) {}
}
```

Never add Eloquent traits, `$fillable`, or `$casts` to Domain entities.