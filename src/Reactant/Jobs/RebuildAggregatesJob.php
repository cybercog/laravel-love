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
use Cog\Contracts\Love\Reactant\ReactionTotal\Models\ReactionTotal as ReactionTotalContract;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeContract;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionCounter\Services\ReactionCounterService;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\NullReactionTotal;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

final class RebuildAggregatesJob implements
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

    /**
     * @param \Cog\Contracts\Love\Reactant\Models\Reactant $reactant
     * @param \Cog\Contracts\Love\ReactionType\Models\ReactionType|null $reactionType
     */
    public function __construct(
        ReactantContract $reactant,
        ?ReactionTypeContract $reactionType = null
    ) {
        $this->reactant = $reactant;
        $this->reactionType = $reactionType;
    }

    public function handle(
    ): void {
        /** @var \Illuminate\Database\Eloquent\Builder $query */
        $query = $this->reactant->reactions();

        if ($this->reactionType !== null) {
            $query->where('reaction_type_id', $this->reactionType->getId());
        }

        $counters = $this->reactant->getReactionCounters();

        /** @var \Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter $counter */
        foreach ($counters as $counter) {
            if ($this->reactionType && $counter->isNotReactionOfType($this->reactionType)) {
                continue;
            }

            $counter->update([
                'count' => ReactionCounter::COUNT_DEFAULT,
                'weight' => ReactionCounter::WEIGHT_DEFAULT,
            ]);
        }

        $reactions = $query->get();
        $this->recountCounters($this->reactant, $reactions);
        $this->recountTotal($this->reactant);
    }

    private function recountTotal(
        ReactantContract $reactant
    ): void {
        $counters = $reactant->getReactionCounters();

        if (count($counters) === 0) {
            return;
        }

        $totalCount = ReactionTotal::COUNT_DEFAULT;
        $totalWeight = ReactionTotal::WEIGHT_DEFAULT;

        foreach ($counters as $counter) {
            $totalCount += $counter->getCount();
            $totalWeight += $counter->getWeight();
        }

        /** @var \Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal $reactionTotal */
        $reactionTotal = $this->findOrCreateReactionTotal($reactant);

        $reactionTotal->update([
            'count' => $totalCount,
            'weight' => $totalWeight,
        ]);
    }

    private function recountCounters(
        ReactantContract $reactant,
        iterable $reactions
    ): void {
        $service = new ReactionCounterService($reactant);

        foreach ($reactions as $reaction) {
            $service->addReaction($reaction);
        }
    }

    /**
     * @param \Cog\Contracts\Love\Reactant\Models\Reactant $reactant
     * @return \Cog\Contracts\Love\Reactant\ReactionTotal\Models\ReactionTotal
     */
    private function findOrCreateReactionTotal(
        ReactantContract $reactant
    ): ReactionTotalContract {
        $reactionTotal = $reactant->getReactionTotal();

        if ($reactionTotal instanceof NullReactionTotal) {
            $reactant->createReactionTotal();
            $reactionTotal = $reactant->getReactionTotal();
        }

        return $reactionTotal;
    }
}
