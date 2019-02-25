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

namespace Cog\Laravel\Love\Reactant\Listeners;

use Cog\Laravel\Love\Reactant\ReactionCounter\Services\ReactionCounterService;
use Cog\Laravel\Love\Reactant\ReactionTotal\Services\ReactionTotalService;
use Cog\Laravel\Love\Reaction\Events\ReactionHasBeenRemoved;
use Illuminate\Contracts\Queue\ShouldQueue;

final class DecrementAggregates implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  \Cog\Laravel\Love\Reaction\Events\ReactionHasBeenRemoved  $event
     * @return void
     */
    public function handle(ReactionHasBeenRemoved $event): void
    {
        $reaction = $event->getReaction();
        $reactant = $reaction->getReactant();

        (new ReactionCounterService($reactant))
            ->removeReaction($reaction);

        (new ReactionTotalService($reactant))
            ->removeReaction($reaction);
    }
}
