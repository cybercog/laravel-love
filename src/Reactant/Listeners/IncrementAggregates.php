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
use Illuminate\Contracts\Bus\Dispatcher as DispatcherInterface;

final class IncrementAggregates
{
    private DispatcherInterface $dispatcher;

    public function __construct(
        DispatcherInterface $dispatcher,
    ) {
        $this->dispatcher = $dispatcher;
    }

    public function handle(
        ReactionHasBeenAdded $event,
    ): void {
        $reaction = $event->getReaction();
        $reactant = $reaction->getReactant();

        $this->dispatcher->dispatch(
            new IncrementReactionAggregatesJob($reactant, $reaction),
        );
    }
}
