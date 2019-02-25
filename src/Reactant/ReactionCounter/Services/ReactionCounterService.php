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
use Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter as ReactionCounterContract;
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
        $counter = $this->findOrCreateCounterOfType($reaction->getType());
        $counter->incrementCount(1);
        $counter->incrementWeight($reaction->getWeight());
    }

    public function removeReaction(
        ReactionContract $reaction
    ): void {
        $counter = $this->findOrCreateCounterOfType($reaction->getType());

        if ($counter->getCount() === 0) {
            return;
        }

        $counter->decrementCount(1);
        $counter->decrementWeight($reaction->getWeight());
    }

    private function findCounterOfType(
        ReactionTypeContract $reactionType
    ): ReactionCounterContract {
        return $this->reactant->getReactionCounterOfType($reactionType);
    }

    private function findOrCreateCounterOfType(
        ReactionTypeContract $reactionType
    ): ReactionCounterContract {
        $counter = $this->findCounterOfType($reactionType);
        if ($counter instanceof NullReactionCounter) {
            $this->reactant->createReactionCounterOfType($reactionType);
            $counter = $this->findCounterOfType($reactionType);
        }

        return $counter;
    }
}
