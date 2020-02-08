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

use Cog\Laravel\Love\Reactant\Listeners\DecrementAggregates;
use Cog\Laravel\Love\Reactant\Listeners\IncrementAggregates;
use Cog\Laravel\Love\Reaction\Events\ReactionHasBeenAdded;
use Cog\Laravel\Love\Reaction\Events\ReactionHasBeenRemoved;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

final class LoveEventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerListeners();
    }

    /**
     * Register the Love event listeners.
     *
     * @return void
     */
    private function registerListeners(): void
    {
        Event::listen(ReactionHasBeenAdded::class, IncrementAggregates::class);
        Event::listen(ReactionHasBeenRemoved::class, DecrementAggregates::class);
    }
}
