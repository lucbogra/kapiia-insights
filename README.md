# Kapiia Insights

Application Business Intelligence pour l'analyse anonymisée des parcours de jeunes accueillis dans les centres d'accueil en France.

## Contexte

Kapiia est une application déployée sur plusieurs sites d'accueil, avec une base de données par déploiement. Elle trace le parcours des jeunes (informations personnelles, incidents, transmissions éducatives, parcours scolaire, tests psychotechniques).

**Kapiia Insights** interroge simultanément plusieurs bases Kapiia pour identifier des tendances sur des populations anonymisées. L'anonymisation passe par la création d'**archétypes** (combinaisons de critères démographiques) auxquels sont associées des populations d'usagers correspondants. Les analystes construisent ensuite des **scénarios d'analyse** combinant une population, des filtres comportementaux et des indicateurs à mesurer.

## Stack

- PHP 8.4, Laravel 13
- Filament 5 (panel d'administration)
- Livewire 4, Tailwind 4
- Pest 4 (tests)
- Laravel Boost (assistance IA)

## Architecture

Le projet suit une **architecture hexagonale** avec Domain-Driven Design. Le code métier vit sous `app/BI/`, organisé en trois **bounded contexts** :

- **DataSource** — gestion des connexions aux bases Kapiia externes
- **Profiling** — définition des critères et construction des archétypes
- **Analysis** — scénarios, précisions, indicateurs, exécution des analyses

Chaque contexte suit une structure en trois couches :

{Context}/
├── Domain/           # PHP pur, entités immutables, interfaces
├── Application/      # Use Cases, orchestration
└── Infrastructure/   # Eloquent, drivers, adaptateurs

**Règle de dépendance** : `Analysis` dépend de `Profiling` et `DataSource`, jamais l'inverse.

**Patterns clés** :
- Un dossier par Use Case avec un `Command` (DTO d'entrée) et une classe `UseCase` (logique)
- Registries pour les indicateurs et précisions extensibles
- Repository : interfaces dans `Domain/`, implémentations Eloquent dans `Infrastructure/`

Les instructions détaillées pour les développeurs et les agents IA sont dans `.ai/guidelines/` et `.claude/skills/`.