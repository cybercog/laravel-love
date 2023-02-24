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

namespace Cog\Laravel\Love\Reactable;

use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableInterface;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Str;

/**
 * @mixin Builder
 */
trait ReactableEloquentBuilderTrait
{
    public function whereReactedBy(
        ReacterableInterface $reacterable,
        ?string $reactionTypeName = null,
    ): Builder {
        return $this->whereHas(
            'loveReactant.reactions',
            function (Builder $reactionsQuery) use (
                $reacterable,
                $reactionTypeName,
            ): void {
                $reactionsQuery->where(
                    'reacter_id',
                    $reacterable->getLoveReacter()->getId(),
                );

                if ($reactionTypeName !== null) {
                    $reactionsQuery->where(
                        'reaction_type_id',
                        ReactionType::fromName($reactionTypeName)->getId(),
                    );
                }
            }
        );
    }

    public function whereNotReactedBy(
        ReacterableInterface $reacterable,
        ?string $reactionTypeName = null,
    ): Builder {
        return $this->whereDoesntHave(
            'loveReactant.reactions',
            function (Builder $reactionsQuery) use (
                $reacterable,
                $reactionTypeName,
            ): void {
                $reactionsQuery->where(
                    'reacter_id',
                    $reacterable->getLoveReacter()->getId(),
                );

                if ($reactionTypeName !== null) {
                    $reactionsQuery->where(
                        'reaction_type_id',
                        ReactionType::fromName($reactionTypeName)->getId(),
                    );
                }
            }
        );
    }

    public function joinReactionCounterOfType(
        string $reactionTypeName,
        ?string $alias = null,
    ): Builder {
        $reactionType = ReactionType::fromName($reactionTypeName);
        $alias = $alias === null
            ? 'reaction_' . Str::snake($reactionType->getName())
            : $alias;

        $select = $this->getQuery()->columns ?? ["{$this->getModel()->getTable()}.*"];
        $select[] = $this->raw("COALESCE({$alias}.count, 0) as {$alias}_count");
        $select[] = $this->raw("COALESCE({$alias}.weight, 0) as {$alias}_weight");

        return $this
            ->leftJoin(
                (new ReactionCounter())->getTable() . ' as ' . $alias,
                function (JoinClause $join) use (
                    $reactionType,
                    $alias,
                ): void {
                    $join->on(
                        "{$alias}.reactant_id",
                        '=',
                        "{$this->getModel()->getTable()}.love_reactant_id",
                    );
                    $join->where(
                        "{$alias}.reaction_type_id",
                        $reactionType->getId(),
                    );
                }
            )
            ->select($select);
    }

    public function joinReactionTotal(
        ?string $alias = null,
    ): Builder {
        $alias = $alias === null
            ? 'reaction_total'
            : $alias;

        $select = $this->getQuery()->columns ?? ["{$this->getModel()->getTable()}.*"];
        $select[] = $this->raw("COALESCE({$alias}.count, 0) as {$alias}_count");
        $select[] = $this->raw("COALESCE({$alias}.weight, 0) as {$alias}_weight");

        return $this
            ->leftJoin(
                (new ReactionTotal())->getTable() . ' as ' . $alias,
                "{$alias}.reactant_id",
                '=',
                "{$this->getModel()->getTable()}.love_reactant_id",
            )
            ->select($select);
    }
}
