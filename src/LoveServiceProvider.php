<?php

/*
 * This file is part of Laravel Love.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cog\Laravel\Love;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reaction\Models\Reaction as ReactionContract;
use Cog\Laravel\Love\Console\Commands\Recount;
use Cog\Laravel\Love\Console\Commands\UpgradeV5ToV6;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\Reaction\Observers\ReactionObserver;
use Illuminate\Support\ServiceProvider;

final class LoveServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerConsoleCommands();
        $this->registerObservers();
        $this->registerPublishes();
        $this->registerMigrations();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerContracts();
    }

    /**
     * Register Love's models observers.
     *
     * @return void
     */
    private function registerObservers(): void
    {
        $this->app
            ->make(ReactionContract::class)
            ->observe(ReactionObserver::class);
    }

    /**
     * Register Love's console commands.
     *
     * @return void
     */
    private function registerConsoleCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Recount::class,
                UpgradeV5ToV6::class,
            ]);
        }
    }

    /**
     * Register Love's classes in the container.
     *
     * @return void
     */
    private function registerContracts(): void
    {
        $this->app->bind(ReactantContract::class, Reactant::class);
        $this->app->bind(ReactionContract::class, Reaction::class);
    }

    /**
     * Setup the resource publishing groups for Love.
     *
     * @return void
     */
    private function registerPublishes(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'love-migrations');
        }
    }

    /**
     * Register the Love migrations.
     *
     * @return void
     */
    private function registerMigrations(): void
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }
}
