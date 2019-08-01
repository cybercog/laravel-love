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

namespace Cog\Laravel\Love\Reactant\ReactionTotal\Observers;

use Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal;

final class ReactionTotalObserver
{
    public function creating(
        ReactionTotal $total
    ): void {
        if (is_null($total->getAttributeValue('count'))) {
            $total->setAttribute('count', ReactionTotal::COUNT_DEFAULT);
        }

        if (is_null($total->getAttributeValue('weight'))) {
            $total->setAttribute('weight', ReactionTotal::WEIGHT_DEFAULT);
        }
    }
}
