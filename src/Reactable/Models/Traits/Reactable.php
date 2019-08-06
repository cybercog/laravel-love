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
use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableContract;
use Cog\Laravel\Love\Reactable\Observers\ReactableObserver;
use Cog\Laravel\Love\Reactant\Facades\Reactant as ReactantFacade;
use Cog\Laravel\Love\Reactant\Models\NullReactant;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        ReacterableContract $reacterable,
        ?string $reactionTypeName = null
    ): Builder {
        return $query->whereHas('loveReactant.reactions', function (Builder $reactionsQuery) use ($reacterable, $reactionTypeName) {
            $reactionsQuery->where('reacter_id', $reacterable->getLoveReacter()->getId());
            if (!is_null($reactionTypeName)) {
                $reactionsQuery->where('reaction_type_id', ReactionType::fromName($reactionTypeName)->getId());
            }
        });
    }

    public function scopeWhereNotReactedBy(
        Builder $query,
        ReacterableContract $reacterable,
        ?string $reactionTypeName = null
    ): Builder {
        return $query->whereDoesntHave('loveReactant.reactions', function (Builder $reactionsQuery) use ($reacterable, $reactionTypeName) {
            $reactionsQuery->where('reacter_id', $reacterable->getLoveReacter()->getId());
            if (!is_null($reactionTypeName)) {
                $reactionsQuery->where('reaction_type_id', ReactionType::fromName($reactionTypeName)->getId());
            }
        });
    }

    public function scopeJoinReactionCounterOfType(
        Builder $query,
        string $reactionTypeName,
        ?string $alias = null
    ): Builder {
        $reactionType = ReactionType::fromName($reactionTypeName);
        $alias = is_null($alias) ? 'reaction_' . Str::snake($reactionType->getName()) : $alias;

        $select = $query->getQuery()->columns ?? ["{$this->getTable()}.*"];
        $select[] = DB::raw("COALESCE({$alias}.count, 0) as {$alias}_count");
        $select[] = DB::raw("COALESCE({$alias}.weight, 0) as {$alias}_weight");

        return $query
            ->leftJoin((new ReactionCounter())->getTable() . ' as '. $alias, function (JoinClause $join) use ($reactionType, $alias) {
                $join->on("{$alias}.reactant_id", '=', "{$this->getTable()}.love_reactant_id");
                $join->where("{$alias}.reaction_type_id", $reactionType->getId());
            })
            ->select($select);
    }

    public function scopeJoinReactionTotal(
        Builder $query,
        ?string $alias = null
    ): Builder {
        $alias = is_null($alias) ? 'reaction_total' : $alias;
        $select = $query->getQuery()->columns ?? ["{$this->getTable()}.*"];
        $select[] = DB::raw("COALESCE({$alias}.count, 0) as {$alias}_count");
        $select[] = DB::raw("COALESCE({$alias}.weight, 0) as {$alias}_weight");

        return $query
            ->leftJoin((new ReactionTotal())->getTable() . ' as ' . $alias, "{$alias}.reactant_id", '=', "{$this->getTable()}.love_reactant_id")
            ->select($select);
    }
}
