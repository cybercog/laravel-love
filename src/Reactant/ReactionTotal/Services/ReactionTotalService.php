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
use Cog\Contracts\Love\Reactant\ReactionTotal\Exceptions\ReactionTotalBadValue;
use Cog\Contracts\Love\Reactant\ReactionTotal\Exceptions\ReactionTotalMissing;
use Cog\Contracts\Love\Reactant\ReactionTotal\Models\ReactionTotal as ReactionTotalContract;
use Cog\Contracts\Love\Reaction\Models\Reaction as ReactionContract;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\NullReactionTotal;

final class ReactionTotalService
{
    private $reactant;

    private $reactionTotal;

    public function __construct(ReactantContract $reactant)
    {
        $this->reactant = $reactant;
        $this->reactionTotal = $this->findReactionTotalFor($reactant);
    }

    public function addReaction(ReactionContract $reaction): void
    {
        $this->incrementTotalCount();
        $this->incrementTotalWeight($reaction->getWeight());
    }

    public function removeReaction(ReactionContract $reaction): void
    {
        $this->decrementTotalCount();
        $this->decrementTotalWeight($reaction->getWeight());
    }

    private function incrementTotalCount(int $amount = 1): void
    {
        $this->incrementOrDecrementTotalCount($amount);
    }

    private function decrementTotalCount(int $amount = 1): void
    {
        $amount *= -1;
        $this->incrementOrDecrementTotalCount($amount);
    }

    private function incrementTotalWeight(int $amount = 1): void
    {
        $this->incrementOrDecrementTotalWeight($amount);
    }

    private function decrementTotalWeight(int $amount = 1): void
    {
        $amount *= -1;
        $this->incrementOrDecrementTotalWeight($amount);
    }

    private function incrementOrDecrementTotalCount(int $amount = 1): void
    {
        if ($this->reactionTotal->getCount() + $amount < 0) {
            throw ReactionTotalBadValue::totalCountBelowZero();
        }

        $this->reactionTotal->increment('count', $amount);
    }

    private function incrementOrDecrementTotalWeight(int $amount = 1): void
    {
        $this->reactionTotal->increment('weight', $amount);
    }

    private function findReactionTotalFor(ReactantContract $reactant): ReactionTotalContract
    {
        /** @var \Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal $total */
        $total = $reactant->getReactionTotal();

        if ($total instanceof NullReactionTotal) {
            throw ReactionTotalMissing::forReactant($reactant);
        }

        return $total;
    }
}
