# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Guidelines

Project-specific rules are in `.ai/guidelines/` — read them before making changes:

- `.ai/guidelines/01-architecture.md` — DDD structure, bounded contexts, layer rules
- `.ai/guidelines/02-conventions.md` — naming, use case structure, enum pattern, language
- `.ai/guidelines/03-data-model.md` — ULIDs, pivot tables, criteria sanitization, Kapiia DB
- `.ai/guidelines/04-filament-pitfalls.md` — Filament v5 gotchas, authorization, repeaters
- `.ai/guidelines/05-domain-analysis.md` — Scenarios, Indicators, Precisions, run flow

---

## Application Overview

**kapiia-insights** is a Business Intelligence (BI) dashboard built on Laravel + Filament. Its core purpose is to analyze populations across multiple external database sources, filtered by configurable "archetypes" (demographic/behavioral profiles).

## Architecture

The codebase follows a **Domain-Driven Design** structure under `app/BI/`, divided into three bounded contexts:

### `app/BI/` — Domain Contexts

```
BI/
├── DataSource/     # External DB connections (SourceConnection)
├── Profiling/      # Archetypes & criteria definitions
└── Analysis/       # Query execution & result aggregation
```

Each context uses **layered architecture**:
- `Domain/` — Pure PHP value objects and repository interfaces (no Laravel dependencies)
- `Application/` — Use cases, commands, DTOs, orchestration logic
- `Infrastructure/` — Eloquent models, repository implementations, external drivers

**Key design rules:**
- Domain objects (e.g. `Archetype`, `ArchetypeCriterion`) are immutable `final` classes with `readonly` properties — never Eloquent models.
- Eloquent models live only in `Infrastructure/Persistence/` and are named `*Model` (e.g. `ArchetypeModel`, `SourceConnectionModel`).
- Repositories are bound interface→implementation in `app/Providers/BIServiceProvider.php`.

### Core Analysis Flow

1. `RunAnalysisUseCase` checks cache (`AnalysisResultRepository`) → loads archetype + criteria → builds `ArchetypePopulationFilter`
2. `QueryOrchestrator` iterates over each `SourceConnection`, uses `DynamicConnectionFactory` to create a named Laravel DB connection at runtime (prefix `kapiia_<id>`), and queries via `KapiiaSourceRepository`
3. `ResultAggregator` merges results across sources into a flat payload (incident stats, transmission averages by activity)
4. Result is persisted with a TTL expiry as a cache entry

### Criteria System

`ArchetypeCriterion` defines filterable dimensions (type: `discrete` | `range`). Archetypes store a `criteria_values` JSON map and a `criteria_hash` (SHA-256 of sorted criteria) for deduplication. `PopulationFilterBuilder` converts criteria values into `CriterionInterface` objects (`DiscreteCriterion` / `RangeCriterion`) used to build WHERE clauses on external sources.

### Filament UI (`app/Filament/`)

Three resources: `ArchetypeResource`, `ArchetypeCriterionResource`, `SourceConnectionResource`. Each resource delegates its form and table configuration to dedicated classes:
- `Resources/{Name}/Schemas/{Name}Form.php` — form schema
- `Resources/{Name}/Tables/{Name}Table.php` — table columns/filters
- `Resources/{Name}/Pages/` — standard CRUD pages

The Filament panel is configured in `app/Providers/Filament/AdminPanelProvider.php`.
