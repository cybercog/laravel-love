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

namespace Cog\Laravel\Love\Reactant\ReactionCounter\Observers;

use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;

final class ReactionCounterObserver
{
    public function creating(
        ReactionCounter $counter
    ): void {
        if (is_null($counter->getAttributeValue('count'))) {
            $counter->setAttribute('count', ReactionCounter::COUNT_DEFAULT);
        }

        if (is_null($counter->getAttributeValue('weight'))) {
            $counter->setAttribute('weight', ReactionCounter::WEIGHT_DEFAULT);
        }
    }
}
