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

namespace Cog\Laravel\Love\Reactable\Models\Traits;

use Cog\Contracts\Love\Reactable\Exceptions\AlreadyRegisteredAsLoveReactant;
use Cog\Contracts\Love\Reactant\Facades\Reactant as ReactantFacadeContract;
use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reacter\Models\Reacter as ReacterContract;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeContract;
use Cog\Laravel\Love\Reactable\Observers\ReactableObserver;
use Cog\Laravel\Love\Reactant\Facades\Reactant as ReactantFacade;
use Cog\Laravel\Love\Reactant\Models\NullReactant;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

/**
 * @mixin \Cog\Contracts\Love\Reactable\Models\Reactable
 */
trait Reactable
{
    protected static function bootReactable(): void
    {
        static::observe(ReactableObserver::class);
    }

    public function loveReactant(): BelongsTo
    {
        return $this->belongsTo(Reactant::class, 'love_reactant_id');
    }

    public function getLoveReactant(): ReactantContract
    {
        return $this->getAttribute('loveReactant') ?? new NullReactant($this);
    }

    public function viaLoveReactant(): ReactantFacadeContract
    {
        return new ReactantFacade($this->getLoveReactant());
    }

    public function isRegisteredAsLoveReactant(): bool
    {
        return !$this->isNotRegisteredAsLoveReactant();
    }

    public function isNotRegisteredAsLoveReactant(): bool
    {
        return is_null($this->getAttributeValue('love_reactant_id'));
    }

    public function registerAsLoveReactant(): void
    {
        if ($this->isRegisteredAsLoveReactant()) {
            throw new AlreadyRegisteredAsLoveReactant();
        }

        /** @var \Cog\Contracts\Love\Reactant\Models\Reactant $reactant */
        $reactant = $this->loveReactant()->create([
            'type' => $this->getMorphClass(),
        ]);

        $this->setAttribute('love_reactant_id', $reactant->getId());
        $this->save();
    }

    public function scopeWhereReactedBy(
        Builder $query,
        ReacterContract $reacter,
        ?ReactionTypeContract $reactionType = null
    ): Builder {
        return $query->whereHas('loveReactant.reactions', function (Builder $reactionsQuery) use ($reacter, $reactionType) {
            $reactionsQuery->where('reacter_id', $reacter->getId());
            if (!is_null($reactionType)) {
                $reactionsQuery->where('reaction_type_id', $reactionType->getId());
            }
        });
    }

    public function scopeJoinReactionCounterOfType(
        Builder $query,
        ReactionTypeContract $reactionType
    ): Builder {
        $select = $query->getQuery()->columns ?? ["{$this->getTable()}.*"];
        $select[] = DB::raw('COALESCE(lrrc.count, 0) as reactions_count');
        $select[] = DB::raw('COALESCE(lrrc.weight, 0) as reactions_weight');

        return $query
            ->leftJoin((new ReactionCounter())->getTable() . ' as lrrc', function (JoinClause $join) use ($reactionType) {
                $join->on('lrrc.reactant_id', '=', "{$this->getTable()}.love_reactant_id");
                $join->where('lrrc.reaction_type_id', $reactionType->getId());
            })
            ->select($select);
    }

    public function scopeJoinReactionTotal(
        Builder $query
    ): Builder {
        $select = $query->getQuery()->columns ?? ["{$this->getTable()}.*"];
        $select[] = DB::raw('COALESCE(lrrt.count, 0) as reactions_total_count');
        $select[] = DB::raw('COALESCE(lrrt.weight, 0) as reactions_total_weight');

        return $query
            ->leftJoin((new ReactionTotal())->getTable() . ' as lrrt', 'lrrt.reactant_id', '=', "{$this->getTable()}.love_reactant_id")
            ->select($select);
    }
}
