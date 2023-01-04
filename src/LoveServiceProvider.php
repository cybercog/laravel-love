<?php

/*
 * This file is part of Laravel Love.
 *
 * (c) Anton Komarev <anton@komarev.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cog\Laravel\Love;

use Cog\Laravel\Love\Console\Commands\ReactionTypeAdd;
use Cog\Laravel\Love\Console\Commands\Recount;
use Cog\Laravel\Love\Console\Commands\RegisterReactants;
use Cog\Laravel\Love\Console\Commands\RegisterReacters;
use Cog\Laravel\Love\Console\Commands\SetupReactable;
use Cog\Laravel\Love\Console\Commands\SetupReacterable;
use Cog\Laravel\Love\Console\Commands\UpgradeV5ToV6;
use Cog\Laravel\Love\Console\Commands\UpgradeV7ToV8;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionCounter\Observers\ReactionCounterObserver;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal;
use Cog\Laravel\Love\Reactant\ReactionTotal\Observers\ReactionTotalObserver;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\Reaction\Observers\ReactionObserver;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

final class LoveServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
    }

    /**
     * Bootstrap the application events.
     */
    public function boot(): void
    {
        $this->configure();
        $this->registerConsoleCommands();
        $this->registerObservers();
        $this->registerPublishes();
        $this->registerMigrations();
    }

    /**
     * Determine if we should register default migrations.
     */
    private function shouldLoadDefaultMigrations(): bool
    {
        return Config::get('love.load_default_migrations', true);
    }

    /**
     * Register Love's models observers.
     */
    private function registerObservers(): void
    {
        Reaction::observe(ReactionObserver::class);
        ReactionCounter::observe(ReactionCounterObserver::class);
        ReactionTotal::observe(ReactionTotalObserver::class);
    }

    /**
     * Register Love's console commands.
     */
    private function registerConsoleCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ReactionTypeAdd::class,
                Recount::class,
                SetupReactable::class,
                SetupReacterable::class,
                RegisterReactants::class,
                RegisterReacters::class,
                UpgradeV5ToV6::class,
                UpgradeV7ToV8::class,
            ]);
        }
    }

    /**
     * Setup the resource publishing groups for Love.
     */
    private function registerPublishes(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/love.php' => config_path('love.php'),
            ], 'love-config');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'love-migrations');
        }
    }

    /**
     * Register the Love migrations.
     */
    private function registerMigrations(): void
    {
        if ($this->app->runningInConsole() && $this->shouldLoadDefaultMigrations()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    /**
     * Merge Love configuration with the application configuration.
     */
    private function configure(): void
    {
        if (!$this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__ . '/../config/love.php', 'love');
        }
    }
}
