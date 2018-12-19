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

use Cog\Contracts\Love\Reaction\Models\Reaction as ReactionContract;
use Cog\Laravel\Love\Reactant\ReactionCounter\Services\ReactionCounterService;
use Cog\Laravel\Love\Reactant\ReactionTotality\Services\ReactionTotalityService;
use Cog\Laravel\Love\Reaction\Events\ReactionWasCreated;
use Cog\Laravel\Love\Reaction\Events\ReactionWasDeleted;
use Cog\Laravel\Love\Reaction\Models\Reaction;

final class ReactionObserver
{
    public function created(ReactionContract $reaction): void
    {
        event(new ReactionWasCreated($reaction));

        // TODO: Remove statistics updates to background jobs
        // TODO: Remove `fresh` (added to reload changes made in previous service calls)
        $reactant = $reaction->getReactant()->fresh();

        (new ReactionCounterService($reactant))
            ->addReaction($reaction);

        (new ReactionTotalityService($reactant))
            ->addReaction($reaction);
    }

    public function deleted(Reaction $reaction): void
    {
        event(new ReactionWasDeleted($reaction));

        // TODO: Remove statistics updates to background jobs
        // TODO: Remove `fresh` (added to reload changes made in previous service calls)
        $reactant = $reaction->getReactant()->fresh();

        (new ReactionCounterService($reactant))
            ->removeReaction($reaction);

        (new ReactionTotalityService($reactant))
            ->removeReaction($reaction);
    }
}
