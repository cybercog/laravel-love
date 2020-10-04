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

namespace Cog\Laravel\Love\Reactant\ReactionCounter\Services;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantInterface;
use Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter as ReactionCounterInterface;
use Cog\Contracts\Love\Reaction\Models\Reaction as ReactionInterface;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeInterface;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\NullReactionCounter;

final class ReactionCounterService
{
    /**
     * @var ReactantInterface
     */
    private $reactant;

    public function __construct(
        ReactantInterface $reactant
    ) {
        $this->reactant = $reactant;
    }

    public function addReaction(
        ReactionInterface $reaction
    ): void {
        $counter = $this->findOrCreateCounterOfType($reaction->getType());
        $counter->incrementCount(1);
        $counter->incrementWeight($reaction->getWeight());
    }

    public function removeReaction(
        ReactionInterface $reaction
    ): void {
        $counter = $this->findOrCreateCounterOfType($reaction->getType());

        if ($counter->getCount() === 0) {
            return;
        }

        $counter->decrementCount(1);
        $counter->decrementWeight($reaction->getWeight());
    }

    private function findCounterOfType(
        ReactionTypeInterface $reactionType
    ): ReactionCounterInterface {
        return $this->reactant->getReactionCounterOfType($reactionType);
    }

    private function findOrCreateCounterOfType(
        ReactionTypeInterface $reactionType
    ): ReactionCounterInterface {
        $counter = $this->findCounterOfType($reactionType);
        if ($counter instanceof NullReactionCounter) {
            $this->reactant->createReactionCounterOfType($reactionType);
            $counter = $this->findCounterOfType($reactionType);
        }

        return $counter;
    }
}
