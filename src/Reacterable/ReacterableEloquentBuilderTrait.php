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

namespace Cog\Laravel\Love\Reacterable;

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableInterface;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin Builder
 */
trait ReacterableEloquentBuilderTrait
{
    public function whereReactedTo(
        ReactableInterface $reactable,
        ?string $reactionTypeName = null,
    ): Builder {
        return $this->whereHas(
            'loveReacter.reactions',
            function (Builder $reactionsQuery) use (
                $reactable,
                $reactionTypeName,
            ): void {
                $reactionsQuery->where(
                    'reactant_id',
                    $reactable->getLoveReactant()->getId(),
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

    public function whereNotReactedTo(
        ReactableInterface $reactable,
        ?string $reactionTypeName = null,
    ): Builder {
        return $this->whereDoesntHave(
            'loveReacter.reactions',
            function (Builder $reactionsQuery) use (
                $reactable,
                $reactionTypeName,
            ): void {
                $reactionsQuery->where(
                    'reactant_id',
                    $reactable->getLoveReactant()->getId(),
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

    public function whereReactedToBetween(
        ReactableInterface $reactable,
        DateTimeInterface $reactedAtFrom,
        DateTimeInterface $reactedAtTo,
        ?string $reactionTypeName = null,
    ): Builder {
        return $this->whereHas(
            'loveReacter.reactions',
            function (Builder $reactionsQuery) use (
                $reactable,
                $reactedAtFrom,
                $reactedAtTo,
                $reactionTypeName,
            ): void {
                $reactionsQuery->where(
                    'reactant_id',
                    $reactable->getLoveReactant()->getId(),
                );

                $reactionsQuery->whereBetween(
                    (new Reaction())->getTable() . '.created_at',
                    [$reactedAtFrom, $reactedAtTo],
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
}
