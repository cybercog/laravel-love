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

namespace Cog\Laravel\Love\Reactant\ReactionSummary\Services;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reactant\ReactionSummary\Models\ReactionSummary as ReactionSummaryContract;

class ReactionSummaryService
{
    private $reactant;

    private $reactionSummary;

    public function __construct(ReactantContract $reactant)
    {
        $this->reactant = $reactant;
        $this->reactionSummary = $this->findOrCreateReactionSummaryOf($reactant);
    }

    public function incrementTotalCount(int $amount = 1): void
    {
        $this->incrementOrDecrementTotalCount($amount);
    }

    public function decrementTotalCount(int $amount = 1): void
    {
        $amount *= -1;
        $this->incrementOrDecrementTotalCount($amount);
    }

    public function incrementTotalWeight(int $amount = 1): void
    {
        $this->incrementOrDecrementTotalWeight($amount);
    }

    public function decrementTotalWeight(int $amount = 1): void
    {
        $amount *= -1;
        $this->incrementOrDecrementTotalWeight($amount);
    }

    private function incrementOrDecrementTotalCount(int $amount = 1): void
    {
        if ($this->reactionSummary->getAttribute('total_count') + $amount < 0) {
            throw new \RuntimeException('ReactionSummary total_count could not be below zero.');
        }

        $this->reactionSummary->increment('total_count', $amount);
    }

    private function incrementOrDecrementTotalWeight(int $amount = 1): void
    {
        $this->reactionSummary->increment('total_weight', $amount);
    }

    private function findOrCreateReactionSummaryOf(ReactantContract $reactant): ReactionSummaryContract
    {
        /** @var \Cog\Laravel\Love\Reactant\ReactionSummary\Models\ReactionSummary $summary */
        $summary = $reactant->reactionSummary()->first();

        if (is_null($summary)) {
            $summary = $reactant->reactionSummary()->create([
                'total_count' => 0,
                'total_weight' => 0,
            ]);
        }

        return $summary;
    }
}
