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

use Cog\Laravel\Love\Reactant\Jobs\IncrementReactionAggregatesJob;
use Cog\Laravel\Love\Reaction\Events\ReactionHasBeenAdded;
use Illuminate\Contracts\Bus\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Queue\ShouldQueue as ShouldQueueContract;

final class IncrementAggregates implements
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
     * @param  \Cog\Laravel\Love\Reaction\Events\ReactionHasBeenAdded  $event
     * @return void
     */
    public function handle(ReactionHasBeenAdded $event): void
    {
        $reaction = $event->getReaction();
        $reactant = $reaction->getReactant();

        $this->dispatcher->dispatch(
            new IncrementReactionAggregatesJob($reactant, $reaction)
        );
    }
}
