<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Laravel\Likeable\Providers;

use Cog\Contracts\Likeable\Like\Models\Like as LikeContract;
use Cog\Contracts\Likeable\LikeCounter\Models\LikeCounter as LikeCounterContract;
use Cog\Contracts\Likeable\Services\LikeableService as LikeableServiceContract;
use Cog\Laravel\Likeable\Console\Commands\Recount;
use Cog\Laravel\Likeable\Like\Models\Like;
use Cog\Laravel\Likeable\Like\Observers\LikeObserver;
use Cog\Laravel\Likeable\LikeCounter\Models\LikeCounter;
use Cog\Laravel\Likeable\Services\LikeableService;
use Illuminate\Support\ServiceProvider;

/**
 * Class LikeableServiceProvider.
 *
 * @package Cog\Laravel\Likeable\Providers
 */
class LikeableServiceProvider extends ServiceProvider
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
     * Register Likeable's models observers.
     *
     * @return void
     */
    protected function registerObservers()
    {
        $this->app->make(LikeContract::class)->observe(LikeObserver::class);
    }

    /**
     * Register Likeable's console commands.
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
     * Register Likeable's classes in the container.
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
     * Setup the resource publishing groups for Likeable.
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
}
