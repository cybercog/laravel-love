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

namespace Cog\Laravel\Love\Reactant\Listeners;

use Cog\Laravel\Love\Reactant\ReactionCounter\Services\ReactionCounterService;
use Cog\Laravel\Love\Reactant\ReactionTotal\Services\ReactionTotalService;
use Cog\Laravel\Love\Reaction\Events\ReactionHasBeenAdded;
use Illuminate\Contracts\Queue\ShouldQueue;

final class IncrementAggregates implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  \Cog\Laravel\Love\Reaction\Events\ReactionHasBeenAdded  $event
     * @return void
     */
    public function handle(ReactionHasBeenAdded $event): void
    {
        $reaction = $event->getReaction();
        $reactant = $reaction->getReactant();

        (new ReactionCounterService($reactant))
            ->addReaction($reaction);

        (new ReactionTotalService($reactant))
            ->addReaction($reaction);
    }
}
