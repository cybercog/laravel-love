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

namespace Cog\Laravel\Love\Reaction\Observers;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Services\ReactionCounterService;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;

class ReactionObserver
{
    public function created(Reaction $reaction): void
    {
        (new ReactionCounterService($this->reactantOf($reaction)))
            ->incrementCounterOfType($this->reactionTypeOf($reaction));
    }

    public function deleted(Reaction $reaction): void
    {
        (new ReactionCounterService($this->reactantOf($reaction)))
            ->decrementCounterOfType($this->reactionTypeOf($reaction));
    }

    // TODO: (?) $reaction->getReactant();
    private function reactantOf(Reaction $reaction): Reactant
    {
        /** @var \Cog\Laravel\Love\Reactant\Models\Reactant $reactant */
        $reactant = $reaction->reactant()->first();

        return $reactant;
    }

    // TODO: (?) $reaction->getType();
    private function reactionTypeOf(Reaction $reaction): ReactionType
    {
        /** @var \Cog\Laravel\Love\ReactionType\Models\ReactionType $type */
        $type = $reaction->type()->first();

        return $type;
    }
}
