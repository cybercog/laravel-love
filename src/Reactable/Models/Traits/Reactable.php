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

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reacter\Models\Reacter as ReacterContract;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeContract;
use Cog\Laravel\Love\Reactant\Models\NullReactant;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\JoinClause;

/**
 * @mixin \Cog\Contracts\Love\Reactable\Models\Reactable
 */
trait Reactable
{
    protected static function bootReactable(): void
    {
        static::creating(function (ReactableContract $reactable) {
            if ($reactable->isNotRegisteredAsLoveReactant()) {
                $reactant = Reactant::query()->create([
                    'type' => $reactable->getMorphClass(),
                ]);

                $reactable->setAttribute('love_reactant_id', $reactant->getId());
            }
        });
    }

    public function loveReactant(): BelongsTo
    {
        return $this->belongsTo(Reactant::class, 'love_reactant_id');
    }

    public function getLoveReactant(): ReactantContract
    {
        return $this->getAttribute('loveReactant') ?? new NullReactant($this);
    }

    public function isRegisteredAsLoveReactant(): bool
    {
        return !$this->isNotRegisteredAsLoveReactant();
    }

    public function isNotRegisteredAsLoveReactant(): bool
    {
        return is_null($this->getAttributeValue('love_reactant_id'));
    }

    public function scopeWhereReactedBy(Builder $query, ReacterContract $reacter): Builder
    {
        return $query->whereHas('loveReactant.reactions', function (Builder $reactionsQuery) use ($reacter) {
            $reactionsQuery->where('reacter_id', $reacter->getId());
        });
    }

    public function scopeWhereReactedByWithType(
        Builder $query,
        ReacterContract $reacter,
        ReactionTypeContract $reactionType
    ): Builder {
        return $query->whereHas('loveReactant.reactions', function (Builder $reactionsQuery) use ($reacter, $reactionType) {
            $reactionsQuery->where('reacter_id', $reacter->getId());
            $reactionsQuery->where('reaction_type_id', $reactionType->getId());
        });
    }

    public function scopeJoinReactionCounterWithType(Builder $query, ReactionTypeContract $reactionType): Builder
    {
        $select = $query->getQuery()->columns ?? ["{$this->getTable()}.*"];
        $select[] = 'lrrc.count as reactions_count';
        $select[] = 'lrrc.weight as reactions_weight';

        return $query
            ->join((new ReactionCounter())->getTable() . ' as lrrc', function (JoinClause $join) use ($reactionType) {
                $join->on('lrrc.reactant_id', '=', "{$this->getTable()}.love_reactant_id");
                $join->where('lrrc.reaction_type_id', $reactionType->getId());
            })
            ->select($select);
    }

    public function scopeJoinReactionTotal(Builder $query): Builder
    {
        $select = $query->getQuery()->columns ?? ["{$this->getTable()}.*"];
        $select[] = 'lrrt.count as reactions_total_count';
        $select[] = 'lrrt.weight as reactions_total_weight';

        return $query
            ->join((new ReactionTotal())->getTable() . ' as lrrt', 'lrrt.reactant_id', '=', "{$this->getTable()}.love_reactant_id")
            ->select($select);
    }
}
