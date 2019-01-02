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

namespace Cog\Laravel\Love\Reactant\ReactionCounter\Services;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reactant\ReactionCounter\Exceptions\ReactionCounterBadValue;
use Cog\Contracts\Love\Reactant\ReactionCounter\Exceptions\ReactionCounterMissing;
use Cog\Contracts\Love\Reaction\Models\Reaction as ReactionContract;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeContract;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\NullReactionCounter;

final class ReactionCounterService
{
    private $reactant;

    public function __construct(
        ReactantContract $reactant
    ) {
        $this->reactant = $reactant;
    }

    public function addReaction(
        ReactionContract $reaction
    ): void {
        $this->createMissingCounterOfType($reaction->getType());
        $this->incrementCountOfType($reaction->getType());
        $this->incrementWeightOfType($reaction->getType(), $reaction->getWeight());
    }

    public function removeReaction(
        ReactionContract $reaction
    ): void {
        $this->decrementCountOfType($reaction->getType());
        $this->decrementWeightOfType($reaction->getType(), $reaction->getWeight());
    }

    private function incrementCountOfType(
        ReactionTypeContract $reactionType,
        int $amount = 1
    ): void {
        $this->incrementOrDecrementCountOfType($reactionType, $amount);
    }

    private function decrementCountOfType(
        ReactionTypeContract $reactionType,
        int $amount = 1
    ): void {
        $amount *= -1;
        $this->incrementOrDecrementCountOfType($reactionType, $amount);
    }

    private function incrementWeightOfType(
        ReactionTypeContract $reactionType,
        int $amount = 1
    ): void {
        $this->incrementOrDecrementWeightOfType($reactionType, $amount);
    }

    private function decrementWeightOfType(
        ReactionTypeContract $reactionType,
        int $amount = 1
    ): void {
        $amount *= -1;
        $this->incrementOrDecrementWeightOfType($reactionType, $amount);
    }

    private function incrementOrDecrementCountOfType(
        ReactionTypeContract $reactionType,
        int $amount = 1
    ): void {
        $counter = $this->reactant->getReactionCounterOfType($reactionType);

        // TODO: Test it
        if ($counter instanceof NullReactionCounter) {
            throw ReactionCounterMissing::forReactantOfReactionType($this->reactant, $reactionType);
        }

        if ($counter->getCount() + $amount < 0) {
            throw ReactionCounterBadValue::countBelowZero();
        }

        $counter->incrementCount($amount);
    }

    private function incrementOrDecrementWeightOfType(
        ReactionTypeContract $reactionType,
        int $amount = 1
    ): void {
        $counter = $this->reactant->getReactionCounterOfType($reactionType);

        // TODO: Test it
        if ($counter instanceof NullReactionCounter) {
            throw ReactionCounterMissing::forReactantOfReactionType($this->reactant, $reactionType);
        }

        $counter->incrementWeight($amount);
    }

    private function createMissingCounterOfType(
        ReactionTypeContract $reactionType
    ): void {
        $counter = $this->reactant->getReactionCounterOfType($reactionType);
        if ($counter instanceof NullReactionCounter) {
            $this->reactant->createReactionCounterOfType($reactionType);
        }
    }
}
