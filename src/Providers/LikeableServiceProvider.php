<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Likeable\Providers;

use Cog\Likeable\Console\LikeableRecountCommand;
use Cog\Likeable\Contracts\Like as LikeContract;
use Cog\Likeable\Contracts\LikeableService as LikeableServiceContract;
use Cog\Likeable\Contracts\LikeCounter as LikeCounterContract;
use Cog\Likeable\Models\Like;
use Cog\Likeable\Models\LikeCounter;
use Cog\Likeable\Observers\LikeObserver;
use Cog\Likeable\Services\LikeableService;
use Illuminate\Support\ServiceProvider;

/**
 * Class LikeableServiceProvider.
 *
 * @package Cog\Likeable\Providers
 */
class LikeableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                LikeableRecountCommand::class,
            ]);

            $this->publishes([
                realpath(__DIR__ . '/../../database/migrations') => database_path('migrations'),
            ], 'migrations');
        }

        $this->bootObservers();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(LikeContract::class, Like::class);
        $this->app->bind(LikeCounterContract::class, LikeCounter::class);
        $this->app->singleton(LikeableServiceContract::class, LikeableService::class);
    }

    /**
     * Package models observers.
     */
    protected function bootObservers()
    {
        $this->app->make(LikeContract::class)->observe(new LikeObserver());
    }
}
