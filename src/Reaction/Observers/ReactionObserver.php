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

namespace Cog\Laravel\Love\Reaction\Observers;

use Cog\Laravel\Love\Reactant\ReactionCounter\Services\ReactionCounterService;
use Cog\Laravel\Love\Reactant\ReactionTotal\Services\ReactionTotalService;
use Cog\Laravel\Love\Reaction\Events\ReactionHasBeenAdded;
use Cog\Laravel\Love\Reaction\Events\ReactionHasBeenRemoved;
use Cog\Laravel\Love\Reaction\Models\Reaction;

final class ReactionObserver
{
    public function created(
        Reaction $reaction
    ): void {
        event(new ReactionHasBeenAdded($reaction));

        // TODO: Move statistics updates to background jobs
        $reactant = $reaction->getReactant();

        (new ReactionCounterService($reactant))
            ->addReaction($reaction);

        (new ReactionTotalService($reactant))
            ->addReaction($reaction);
    }

    public function deleted(
        Reaction $reaction
    ): void {
        event(new ReactionHasBeenRemoved($reaction));

        // TODO: Move statistics updates to background jobs
        $reactant = $reaction->getReactant();

        (new ReactionCounterService($reactant))
            ->removeReaction($reaction);

        (new ReactionTotalService($reactant))
            ->removeReaction($reaction);
    }
}
