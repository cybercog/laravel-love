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
    public static function isReactionOfTypeName(
        ReactionContract $reaction,
        string $typeName
    ): bool {
        return $reaction->isOfType(ReactionType::fromName($typeName));
    }

    public static function isReactionNotOfTypeName(
        ReactionContract $reaction,
        string $typeName
    ): bool {
        return !self::isReactionOfTypeName($reaction, $typeName);
    }

    public static function isReacterableReactedToWithTypeName(
        ?ReacterableContract $reacterable,
        ReactableContract $reactable,
        string $typeName
    ): bool {
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

        return $reacter->isReactedToWithType($reactant, ReactionType::fromName($typeName));
    }

    public static function isReacterableNotReactedToWithTypeName(
        ?ReacterableContract $reacterable,
        ReactableContract $reactable,
        string $typeName
    ): bool {
        return !self::isReacterableReactedToWithTypeName($reacterable, $reactable, $typeName);
    }

    public static function isReacterableReactedTo(
        ?ReacterableContract $reacterable,
        ReactableContract $reactable
    ): bool {
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
    ): bool {
        return !self::isReacterableReactedTo($reacterable, $reactable);
    }

    public static function isReactableReactedByWithTypeName(
        ReactableContract $reactable,
        ?ReacterableContract $reacterable,
        string $typeName
    ): bool {
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

        return $reactant->isReactedByWithType($reacter, ReactionType::fromName($typeName));
    }

    public static function isReactableNotReactedByWithTypeName(
        ReactableContract $reactable,
        ?ReacterableContract $reacterable,
        string $typeName
    ): bool {
        return !self::isReactableReactedByWithTypeName($reactable, $reacterable, $typeName);
    }

    public static function isReactableReactedBy(
        ReactableContract $reactable,
        ?ReacterableContract $reacterable
    ): bool {
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

        return $reactant->isReactedBy($reacter);
    }

    public static function isReactableNotReactedBy(
        ReactableContract $reactable,
        ?ReacterableContract $reacterable
    ): bool {
        return !self::isReactableReactedBy($reactable, $reacterable);
    }

    public static function getReactableReactionsCountForTypeName(
        ReactableContract $reactable,
        string $typeName
    ): int {
        $reactant = $reactable->getReactant();
        if ($reactant instanceof NullReactant) {
            return 0;
        }

        return $reactant
            ->getReactionCounterOfType(ReactionType::fromName($typeName))
            ->getCount();
    }

    public static function getReactableReactionsWeightForTypeName(
        ReactableContract $reactable,
        string $typeName
    ): int {
        $reactant = $reactable->getReactant();
        if ($reactant instanceof NullReactant) {
            return 0;
        }

        return $reactant
            ->getReactionCounterOfType(ReactionType::fromName($typeName))
            ->getWeight();
    }

    public static function getReactableReactionsTotalCount(
        ReactableContract $reactable
    ): int {
        $reactant = $reactable->getReactant();
        if ($reactant instanceof NullReactant) {
            return 0;
        }

        return $reactant->getReactionTotal()->getCount();
    }

    public static function getReactableReactionsTotalWeight(
        ReactableContract $reactable
    ): int {
        $reactant = $reactable->getReactant();
        if ($reactant instanceof NullReactant) {
            return 0;
        }

        return $reactant->getReactionTotal()->getWeight();
    }
}
