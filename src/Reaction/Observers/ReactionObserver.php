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
use Cog\Laravel\Love\Reactant\ReactionSummary\Services\ReactionSummaryService;
use Cog\Laravel\Love\Reaction\Events\ReactionWasCreated;
use Cog\Laravel\Love\Reaction\Events\ReactionWasDeleted;
use Cog\Laravel\Love\Reaction\Models\Reaction;

class ReactionObserver
{
    public function created(Reaction $reaction): void
    {
        event(new ReactionWasCreated($reaction));

        $reactant = $reaction->getReactant();

        (new ReactionCounterService($reactant))
            ->incrementCounterOfType($reaction->getType());

        $summaryService = new ReactionSummaryService($reactant);
        $summaryService->incrementTotalCount();
        $summaryService->incrementTotalWeight($reaction->getWeight());
    }

    public function deleted(Reaction $reaction): void
    {
        event(new ReactionWasDeleted($reaction));

        $reactant = $reaction->getReactant();

        (new ReactionCounterService($reactant))
            ->decrementCounterOfType($reaction->getType());

        $summaryService = new ReactionSummaryService($reactant);
        $summaryService->decrementTotalCount();
        $summaryService->decrementTotalWeight($reaction->getWeight());
    }
}
