# AGENTS.md

This file provides guidance to LLM Agents when working with code in this repository.

## Project Overview

Laravel Love is a PHP package (`cybercog/laravel-love`) that adds reaction functionality (like, dislike, upvote, etc.) to Laravel Eloquent models. It supports Laravel 9-13 and PHP 8.0-8.5.

## Commands

All commands run through Docker. Services: `php81`, `php82`, `php83`, `php84`, `php85`.

```bash
# Build and start containers
docker compose up -d --build

# Install dependencies
docker compose exec php85 composer install

# Run all tests (uses in-memory SQLite)
docker compose exec php85 composer test

# Run a single test file
docker compose exec php85 vendor/bin/phpunit tests/Unit/Reactant/Models/ReactantTest.php

# Run a single test method
docker compose exec php85 vendor/bin/phpunit --filter test_method_name

# Static analysis
docker compose exec php85 composer phpstan
```

## Namespaces & Autoloading

- `Cog\Contracts\Love\` → `contracts/` (interfaces)
- `Cog\Laravel\Love\` → `src/` (implementations)
- `Cog\Tests\Laravel\Love\` → `tests/` (tests)

## Architecture

### Core Domain Model

The package models a **reaction system** with these entities:

- **Reacter** – an entity that performs reactions (e.g., a User). Created via the `Reacterable` trait on an Eloquent model.
- **Reactant** – an entity that receives reactions (e.g., a Post). Created via the `Reactable` trait on an Eloquent model.
- **ReactionType** – a named type with a mass/weight (e.g., Like with mass +1, Dislike with mass -1).
- **Reaction** – a single reaction linking a Reacter to a Reactant with a ReactionType and a rate (0.01–99.99).

Aggregate tables maintain denormalized counts:
- **ReactionCounter** – per-type count and weight for each Reactant.
- **ReactionTotal** – total count and weight across all types for each Reactant.

### Key Design Patterns

- **Traits as entry points**: Models use `Reactable` and `Reacterable` traits which auto-register the model as a Reactant/Reacter via observers.
- **Facade objects** (not Laravel Facades): `Reacter\Facades\Reacter` and `Reactant\Facades\Reactant` wrap models to provide a fluent API (`reactTo()`, `unreactTo()`, `hasReactedTo()`).
- **Null Object pattern**: `NullReactant`, `NullReacter`, `NullReactionCounter`, `NullReactionTotal` handle unregistered entities without null checks.
- **Event-driven aggregates**: `ReactionHasBeenAdded`/`ReactionHasBeenRemoved` events trigger listeners that increment/decrement ReactionCounter and ReactionTotal.
- **Contracts separate from implementation**: All interfaces live in `contracts/`, implementations in `src/`.

### Service Providers

- `LoveServiceProvider` – registers config, migrations, console commands, and the ReactionObserver.
- `LoveEventServiceProvider` – maps reaction events to aggregate update listeners.

### Database

Six tables (names configurable in `config/love.php`): `love_reacters`, `love_reactants`, `love_reaction_types`, `love_reactions`, `love_reactant_reaction_counters`, `love_reactant_reaction_totals`.

## Testing

Tests use Orchestra Testbench with in-memory SQLite. The base `TestCase` class (`tests/TestCase.php`) handles migrations, factory registration, and morph map setup. Test stubs live in `tests/Stubs/Models/` and factories in `tests/database/factories/`.

## Code Conventions

- All PHP files use `declare(strict_types=1)`.
- All files include the copyright header block.
- PSR-12 coding style.
