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

namespace Cog\Laravel\Love\Reactant\Jobs;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter as ReactionCounterContract;
use Cog\Contracts\Love\Reactant\ReactionTotal\Models\ReactionTotal as ReactionTotalContract;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeContract;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionCounter\Services\ReactionCounterService;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\NullReactionTotal;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

final class RebuildReactionAggregatesJob implements
    ShouldQueue
{
    use Dispatchable;
    use Queueable;

    /**
     * @var \Cog\Contracts\Love\Reactant\Models\Reactant
     */
    private $reactant;

    /**
     * @var \Cog\Contracts\Love\ReactionType\Models\ReactionType|null
     */
    private $reactionType;

    public function __construct(
        ReactantContract $reactant,
        ?ReactionTypeContract $reactionType = null
    ) {
        $this->reactant = $reactant;
        $this->reactionType = $reactionType;
    }

    public function handle(
    ): void {
        $this->recountReactionCounters();
        $this->recountReactionTotal();
    }

    private function recountReactionCounters(
    ): void {
        $this->resetCountersValues();

        $service = new ReactionCounterService($this->reactant);

        $reactions = $this->collectReactions();
        foreach ($reactions as $reaction) {
            $service->addReaction($reaction);
        }
    }

    private function recountReactionTotal(
    ): void {
        $counters = $this->reactant->getReactionCounters();

        if (count($counters) === 0) {
            return;
        }

        $totalCount = ReactionTotal::COUNT_DEFAULT;
        $totalWeight = ReactionTotal::WEIGHT_DEFAULT;

        foreach ($counters as $counter) {
            $totalCount += $counter->getCount();
            $totalWeight += $counter->getWeight();
        }

        $reactionTotal = $this->findOrCreateReactionTotal();

        /** @var \Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal $reactionTotal */
        $reactionTotal->update([
            'count' => $totalCount,
            'weight' => $totalWeight,
        ]);
    }

    private function findOrCreateReactionTotal(
    ): ReactionTotalContract {
        $reactionTotal = $this->reactant->getReactionTotal();

        if ($reactionTotal instanceof NullReactionTotal) {
            $this->reactant->createReactionTotal();
            $reactionTotal = $this->reactant->getReactionTotal();
        }

        return $reactionTotal;
    }

    private function resetCountersValues(
    ): void {
        $counters = $this->reactant->getReactionCounters();

        foreach ($counters as $counter) {
            if ($this->shouldNotAffectCounter($counter)) {
                continue;
            }

            /** @var \Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter $counter */
            $counter->update([
                'count' => ReactionCounter::COUNT_DEFAULT,
                'weight' => ReactionCounter::WEIGHT_DEFAULT,
            ]);
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function collectReactions(
    ): iterable {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = $this->reactant->reactions();

        if ($this->reactionType !== null) {
            $query->where('reaction_type_id', $this->reactionType->getId());
        }

        return $query->get();
    }

    /**
     * Determine if counter should not be rebuilt.
     *
     * @param ReactionCounterContract $counter
     * @return bool
     */
    private function shouldNotAffectCounter(
        ReactionCounterContract $counter
    ): bool {
        return $this->reactionType !== null
            && $counter->isNotReactionOfType($this->reactionType);
    }
}
