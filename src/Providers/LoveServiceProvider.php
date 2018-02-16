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

namespace Cog\Laravel\Love\Providers;

use Cog\Contracts\Love\Like\Models\Like as LikeContract;
use Cog\Contracts\Love\LikeCounter\Models\LikeCounter as LikeCounterContract;
use Cog\Contracts\Love\Likeable\Services\LikeableService as LikeableServiceContract;
use Cog\Laravel\Love\Console\Commands\Recount;
use Cog\Laravel\Love\Like\Models\Like;
use Cog\Laravel\Love\Like\Observers\LikeObserver;
use Cog\Laravel\Love\Likeable\Services\LikeableService;
use Cog\Laravel\Love\LikeCounter\Models\LikeCounter;
use Illuminate\Support\ServiceProvider;

/**
 * Class LoveServiceProvider.
 *
 * @package Cog\Laravel\Love\Providers
 */
class LoveServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
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
    public function register()
    {
        $this->registerContracts();
    }

    /**
     * Register Love's models observers.
     *
     * @return void
     */
    protected function registerObservers()
    {
        $this->app->make(LikeContract::class)->observe(LikeObserver::class);
    }

    /**
     * Register Love's console commands.
     *
     * @return void
     */
    protected function registerConsoleCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Recount::class,
            ]);
        }
    }

    /**
     * Register Love's classes in the container.
     *
     * @return void
     */
    protected function registerContracts()
    {
        $this->app->bind(LikeContract::class, Like::class);
        $this->app->bind(LikeCounterContract::class, LikeCounter::class);
        $this->app->singleton(LikeableServiceContract::class, LikeableService::class);
    }

    /**
     * Setup the resource publishing groups for Love.
     *
     * @return void
     */
    protected function registerPublishes()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../database/migrations' => database_path('migrations'),
            ], 'migrations');
        }
    }

    /**
     * Register the Love migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        }
    }
}
