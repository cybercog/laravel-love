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

namespace Cog\Laravel\Love\Reactant\Observers;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Laravel\Love\Reactant\ReactionCounter\Services\ReactionCounterService;

final class ReactantObserver
{
    public function created(ReactantContract $reactant): void
    {
        // TODO: Do it in service or in`ReactionCounter` or `Reactant` method?
        // TODO: Call it asynchronously
        // TODO: Cover with tests
        $counterService = new ReactionCounterService($reactant);
        $counterService->createCounters();

        // TODO: Do it in service or in `ReactionTotal` or `Reactant` method?
        // TODO: Call it asynchronously
        // TODO: Cover with tests
        $reactant->createReactionTotal();
    }
}
