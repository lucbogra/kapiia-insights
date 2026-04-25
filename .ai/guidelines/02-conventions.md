# Code Conventions

## Naming

- **Eloquent models**: suffix `Model` (`ScenarioModel`, `ArchetypeModel`)
- **Enums**: suffix `Enum` (`IndicatorTargetEnum`, `PrecisionTypeEnum`)
- **Use Cases**: `{Verb}{Noun}UseCase` with matching `{Verb}{Noun}Command`
- **Repository interfaces**: suffix `RepositoryInterface`
- **Repository implementations**: suffix `Repository` (no prefix like `Eloquent`)
- **Domain entities**: singular noun, no suffix (`Archetype`, `Scenario`)

## Use Case structure

One folder per use case:

Application/UseCase/CreateScenario/
├── CreateScenarioCommand.php
└── CreateScenarioUseCase.php

The Command is a readonly DTO. The UseCase has a single public `execute(Command)` method returning a Domain entity or throwing `\DomainException`.

## Enum pattern

```php
namespace App\BI\Analysis\Domain\Indicator;

enum IndicatorTargetEnum: string
{
    case Incidents     = 'incidents';
    case Transmissions = 'transmissions';
    case Global        = 'global';

    public function label(): string
    {
        return match ($this) {
            self::Incidents     => 'Incidents',
            self::Transmissions => 'Transmissions',
            self::Global        => 'Global',
        };
    }
}
```

Always provide a `label()` method returning the French UI label.

## Repository implementation pattern

```php
final class ArchetypeRepository implements ArchetypeRepositoryInterface
{
    public function findById(string $id): ?Archetype
    {
        $model = ArchetypeModel::find($id);
        return $model ? $this->toDomain($model) : null;
    }

    public function save(Archetype $archetype): void
    {
        ArchetypeModel::updateOrCreate(
            ['id' => $archetype->id],
            [/* mapped fields */],
        );
    }

    private function toDomain(ArchetypeModel $m): Archetype
    {
        return new Archetype(/* ... */);
    }
}
```

## Visibility modifiers

- Use Cases, Domain services, and final classes use `final` and constructor property promotion with `readonly`.
- Private methods for mapping and internal logic. Public surface is minimal.

## Language

- **Comments and exception messages**: French (user-facing)
- **Code identifiers**: English (classes, methods, variables)