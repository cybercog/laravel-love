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

namespace Cog\Laravel\Love\Reactant\ReactionTotal\Services;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reactant\ReactionTotal\Models\ReactionTotal as ReactionTotalContract;
use Cog\Contracts\Love\Reaction\Models\Reaction as ReactionContract;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\NullReactionTotal;

final class ReactionTotalService
{
    private $reactionTotal;

    public function __construct(
        ReactantContract $reactant
    ) {
        $this->reactionTotal = $this->findOrCreateReactionTotalFor($reactant);
    }

    public function addReaction(
        ReactionContract $reaction
    ): void {
        $this->reactionTotal->incrementCount(1);
        $this->reactionTotal->incrementWeight($reaction->getWeight());
    }

    public function removeReaction(
        ReactionContract $reaction
    ): void {
        if ($this->reactionTotal->getCount() === 0) {
            return;
        }

        $this->reactionTotal->incrementCount(-1);
        $amount = -1 * $reaction->getWeight();
        $this->reactionTotal->incrementWeight($amount);
    }

    private function findOrCreateReactionTotalFor(
        ReactantContract $reactant
    ): ReactionTotalContract {
        $total = $reactant->getReactionTotal();

        if ($total instanceof NullReactionTotal) {
            $reactant->createReactionTotal();
            $total = $reactant->getReactionTotal();
        }

        return $total;
    }
}
