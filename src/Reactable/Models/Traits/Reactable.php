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

namespace Cog\Laravel\Love\Reactable\Models\Traits;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reacter\Models\Reacter as ReacterContract;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeContract;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionSummary\Models\ReactionSummary;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait Reactable.
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 * @package Cog\Laravel\Love\Reacterable\Models\Traits
 */
trait Reactable
{
    public function reactant(): BelongsTo
    {
        return $this->belongsTo(Reactant::class, 'love_reactant_id');
    }

    public function getReactant(): ReactantContract
    {
        // TODO: Return `NullReactant` if not set?
        return $this->getAttribute('reactant');
    }

    public function scopeWhereReactedBy(Builder $query, ReacterContract $reacter): Builder
    {
        return $query->whereHas('reactant.reactions', function (Builder $reactionsQuery) use ($reacter) {
            $reactionsQuery->where('reacter_id', $reacter->getKey());
        });
    }

    public function scopeWhereReactedWithTypeBy(
        Builder $query,
        ReacterContract $reacter,
        ReactionTypeContract $reactionType
    ): Builder {
        return $query->whereHas('reactant.reactions', function (Builder $reactionsQuery) use ($reacter, $reactionType) {
            $reactionsQuery->where('reacter_id', $reacter->getKey());
            $reactionsQuery->where('reaction_type_id', $reactionType->getKey());
        });
    }

    public function scopeOrderByReactionsCountOfType(
        Builder $query,
        ReactionTypeContract $reactionType,
        string $direction = 'desc'
    ): Builder {
        return $query
            ->join((new ReactionCounter())->getTable() . ' as lrrc', 'lrrc.reactant_id', '=', $this->getQualifiedKeyName())
            ->where('lrrc.reaction_type_id', $reactionType->getKey())
            ->orderBy('lrrc.count', $direction);
    }

    public function scopeOrderByReactionsCount(Builder $query, string $direction = 'desc'): Builder
    {
        return $query
            ->join((new ReactionSummary())->getTable() . ' as lrrs', 'lrrs.reactant_id', '=', $this->getQualifiedKeyName())
            ->orderBy('lrrs.total_count', $direction);
    }

    public function scopeOrderByReactionsWeight(Builder $query, string $direction = 'desc'): Builder
    {
        return $query
            ->join((new ReactionSummary())->getTable() . ' as lrrs', 'lrrs.reactant_id', '=', $this->getQualifiedKeyName())
            ->orderBy('lrrs.total_weight', $direction);
    }
}
