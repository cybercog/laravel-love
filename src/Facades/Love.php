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

namespace Cog\Laravel\Love\Facades;

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableContract;
use Cog\Contracts\Love\Reaction\Models\Reaction as ReactionContract;
use Cog\Laravel\Love\Reactant\Models\NullReactant;
use Cog\Laravel\Love\Reacter\Models\NullReacter;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;

final class Love
{
    public static function isReactionOfType(ReactionContract $reaction, string $reactionType): bool
    {
        return $reaction->isTypeOf(ReactionType::fromName($reactionType));
    }

    public static function isReactionNotOfType(ReactionContract $reaction, string $reactionType): bool
    {
        return !self::isReactionOfType($reaction, $reactionType);
    }

    public static function isReacterableReactedWithTypeTo(
        ?ReacterableContract $reacterable,
        string $type,
        ReactableContract $reactable
    ) {
        if (is_null($reacterable)) {
            return false;
        }

        $reacter = $reacterable->getReacter();
        if ($reacter instanceof NullReacter) {
            return false;
        }

        $reactant = $reactable->getReactant();
        if ($reactant instanceof NullReactant) {
            return false;
        }

        return $reacter->isReactedWithTypeTo($reactant, ReactionType::fromName($type));
    }

    public static function isReacterableNotReactedWithTypeTo(
        ?ReacterableContract $reacterable,
        string $type,
        ReactableContract $reactable
    ) {
        return !self::isReacterableReactedWithTypeTo($reacterable, $type, $reactable);
    }

    public static function isReacterableReactedTo(
        ?ReacterableContract $reacterable,
        ReactableContract $reactable
    ) {
        if (is_null($reacterable)) {
            return false;
        }

        $reacter = $reacterable->getReacter();
        if ($reacter instanceof NullReacter) {
            return false;
        }

        $reactant = $reactable->getReactant();
        if ($reactant instanceof NullReactant) {
            return false;
        }

        return $reacter->isReactedTo($reactant);
    }

    public static function isReacterableNotReactedTo(
        ?ReacterableContract $reacterable,
        ReactableContract $reactable
    ) {
        return !self::isReacterableReactedTo($reacterable, $reactable);
    }

    public static function getReactableReactionsOfTypeCount(ReactableContract $reactable, string $reactionType): int
    {
        $reactant = $reactable->getReactant();
        if ($reactant instanceof NullReactant) {
            return 0;
        }

        /** @var \Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter $counter */
        $counter = $reactant
            ->getReactionCounters()
            ->where('reaction_type_id', ReactionType::fromName($reactionType)->getKey())
            ->first();

        if (is_null($counter)) {
            return 0;
        }

        return $counter->getCount();
    }

    public static function getReactableReactionsOfTypeWeight(ReactableContract $reactable, string $reactionType): int
    {
        $reactant = $reactable->getReactant();
        if ($reactant instanceof NullReactant) {
            return 0;
        }

        /** @var \Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter $counter */
        $counter = $reactant
            ->getReactionCounters()
            ->where('reaction_type_id', ReactionType::fromName($reactionType)->getKey())
            ->first();

        if (is_null($counter)) {
            return 0;
        }

        return $counter->getWeight();
    }

    public static function getReactableReactionsTotalCount(ReactableContract $reactable): int
    {
        $reactant = $reactable->getReactant();
        if ($reactant instanceof NullReactant) {
            return 0;
        }

        return $reactant->getReactionTotality()->getCount();
    }

    public static function getReactableReactionsTotalWeight(ReactableContract $reactable): int
    {
        $reactant = $reactable->getReactant();
        if ($reactant instanceof NullReactant) {
            return 0;
        }

        return $reactant->getReactionTotality()->getWeight();
    }
}
