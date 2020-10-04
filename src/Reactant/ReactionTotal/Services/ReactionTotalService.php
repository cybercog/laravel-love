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

namespace Cog\Laravel\Love\Reactant\ReactionTotal\Services;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantInterface;
use Cog\Contracts\Love\Reactant\ReactionTotal\Models\ReactionTotal as ReactionTotalInterface;
use Cog\Contracts\Love\Reaction\Models\Reaction as ReactionInterface;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\NullReactionTotal;

final class ReactionTotalService
{
    /**
     * @var ReactionTotalInterface
     */
    private $reactionTotal;

    public function __construct(
        ReactantInterface $reactant
    ) {
        $this->reactionTotal = $this->findOrCreateReactionTotalFor($reactant);
    }

    public function addReaction(
        ReactionInterface $reaction
    ): void {
        $this->reactionTotal->incrementCount(1);
        $this->reactionTotal->incrementWeight($reaction->getWeight());
    }

    public function removeReaction(
        ReactionInterface $reaction
    ): void {
        if ($this->reactionTotal->getCount() === 0) {
            return;
        }

        $this->reactionTotal->decrementCount(1);
        $this->reactionTotal->decrementWeight($reaction->getWeight());
    }

    private function findOrCreateReactionTotalFor(
        ReactantInterface $reactant
    ): ReactionTotalInterface {
        $total = $reactant->getReactionTotal();

        if ($total instanceof NullReactionTotal) {
            $reactant->createReactionTotal();
            $total = $reactant->getReactionTotal();
        }

        return $total;
    }
}
