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

use Cog\Laravel\Love\Reactant\Jobs\DecrementReactionAggregatesJob;
use Cog\Laravel\Love\Reaction\Events\ReactionHasBeenRemoved;
use Illuminate\Contracts\Bus\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Queue\ShouldQueue as ShouldQueueContract;

final class DecrementAggregates implements
    ShouldQueueContract
{
    /**
     * @var \Illuminate\Contracts\Bus\Dispatcher
     */
    private $dispatcher;

    public function __construct(DispatcherContract $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

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

        $this->dispatcher->dispatch(
            new DecrementReactionAggregatesJob($reactant, $reaction)
        );
    }
}
