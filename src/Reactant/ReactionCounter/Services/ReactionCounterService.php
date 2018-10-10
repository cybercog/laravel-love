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
use Cog\Contracts\Love\Reactant\ReactionCounter\Exceptions\ReactionCounterInvalid;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeContract;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;

class ReactionCounterService
{
    private $reactant;

    public function __construct(ReactantContract $reactant)
    {
        $this->reactant = $reactant;
    }

    public function createCounters(): void
    {
        $this->createMissingCounters([]);
    }

    public function createMissingCounters(iterable $existCounters): void
    {
        // TODO: Instead of `all` use custom cacheable static method
        $reactionTypes = ReactionType::all();
        foreach ($reactionTypes as $reactionType) {
            foreach ($existCounters as $counter) {
                if ($counter->isReactionOfType($reactionType)) {
                    continue 2;
                }
            }

            $this->createCounterOfType($reactionType);
        }
    }

    public function incrementCounterOfType(ReactionTypeContract $reactionType, int $amount = 1): void
    {
        $this->incrementOrDecrementOfType($reactionType, $amount);
    }

    public function decrementCounterOfType(ReactionTypeContract $reactionType, int $amount = 1): void
    {
        $amount *= -1;
        $this->incrementOrDecrementOfType($reactionType, $amount);
    }

    private function incrementOrDecrementOfType(ReactionTypeContract $reactionType, int $amount = 1): void
    {
        $counter = $this->reactant->reactionCounters()
            ->where('reaction_type_id', $reactionType->getKey())
            ->first();

        if (is_null($counter)) {
            throw ReactionCounterInvalid::notFoundOfType($reactionType);
        }

        if ($counter->count + $amount < 0) {
            throw ReactionCounterBadValue::countBelowZero();
        }

        $counter->increment('count', $amount);
    }

    private function createCounterOfType(ReactionTypeContract $reactionType): void
    {
        $this->reactant->reactionCounters()->create([
            'reaction_type_id' => $reactionType->getKey(),
            'count' => 0,
        ]);
    }
}
