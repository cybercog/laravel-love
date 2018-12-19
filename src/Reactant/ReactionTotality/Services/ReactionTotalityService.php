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

namespace Cog\Laravel\Love\Reactant\ReactionTotality\Services;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reactant\ReactionTotality\Exceptions\ReactionTotalityBadValue;
use Cog\Contracts\Love\Reactant\ReactionTotality\Exceptions\ReactionTotalityMissing;
use Cog\Contracts\Love\Reactant\ReactionTotality\Models\ReactionTotality as ReactionTotalityContract;
use Cog\Contracts\Love\Reaction\Models\Reaction as ReactionContract;
use Cog\Laravel\Love\Reactant\ReactionTotality\Models\NullReactionTotality;

final class ReactionTotalityService
{
    private $reactant;

    private $reactionTotality;

    public function __construct(ReactantContract $reactant)
    {
        $this->reactant = $reactant;
        $this->reactionTotality = $this->findReactionTotalityFor($reactant);
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
        if ($this->reactionTotality->getCount() + $amount < 0) {
            throw ReactionTotalityBadValue::totalCountBelowZero();
        }

        $this->reactionTotality->increment('count', $amount);
    }

    private function incrementOrDecrementTotalWeight(int $amount = 1): void
    {
        $this->reactionTotality->increment('weight', $amount);
    }

    private function findReactionTotalityFor(ReactantContract $reactant): ReactionTotalityContract
    {
        /** @var \Cog\Laravel\Love\Reactant\ReactionTotality\Models\ReactionTotality $totality */
        $totality = $reactant->getReactionTotality();

        if ($totality instanceof NullReactionTotality) {
            throw ReactionTotalityMissing::forReactant($reactant);
        }

        return $totality;
    }
}
