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

namespace Cog\Laravel\Love\Facades;

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableContract;
use Cog\Contracts\Love\Reaction\Models\Reaction as ReactionContract;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;

/**
 * @deprecated 7.0
 */
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

    public static function isReacterableReactedTo(
        ?ReacterableContract $reacterable,
        ReactableContract $reactable
    ): bool {
        if (is_null($reacterable)) {
            return false;
        }

        return $reacterable
            ->getLoveReacter()
            ->isReactedTo($reactable->getLoveReactant());
    }

    public static function isReacterableNotReactedTo(
        ?ReacterableContract $reacterable,
        ReactableContract $reactable
    ): bool {
        return !self::isReacterableReactedTo($reacterable, $reactable);
    }

    public static function isReacterableReactedToWithTypeName(
        ?ReacterableContract $reacterable,
        ReactableContract $reactable,
        string $typeName
    ): bool {
        if (is_null($reacterable)) {
            return false;
        }

        return $reacterable
            ->getLoveReacter()
            ->isReactedToWithType(
                $reactable->getLoveReactant(),
                ReactionType::fromName($typeName)
            );
    }

    public static function isReacterableNotReactedToWithTypeName(
        ?ReacterableContract $reacterable,
        ReactableContract $reactable,
        string $typeName
    ): bool {
        return !self::isReacterableReactedToWithTypeName(
            $reacterable,
            $reactable,
            $typeName
        );
    }

    public static function isReactableReactedBy(
        ReactableContract $reactable,
        ?ReacterableContract $reacterable
    ): bool {
        if (is_null($reacterable)) {
            return false;
        }

        return $reactable
            ->getLoveReactant()
            ->isReactedBy($reacterable->getLoveReacter());
    }

    public static function isReactableNotReactedBy(
        ReactableContract $reactable,
        ?ReacterableContract $reacterable
    ): bool {
        return !self::isReactableReactedBy($reactable, $reacterable);
    }

    public static function isReactableReactedByWithTypeName(
        ReactableContract $reactable,
        ?ReacterableContract $reacterable,
        string $typeName
    ): bool {
        if (is_null($reacterable)) {
            return false;
        }

        return $reactable
            ->getLoveReactant()
            ->isReactedByWithType(
                $reacterable->getLoveReacter(),
                ReactionType::fromName($typeName)
            );
    }

    public static function isReactableNotReactedByWithTypeName(
        ReactableContract $reactable,
        ?ReacterableContract $reacterable,
        string $typeName
    ): bool {
        return !self::isReactableReactedByWithTypeName(
            $reactable,
            $reacterable,
            $typeName
        );
    }

    public static function getReactableReactionsCountForTypeName(
        ReactableContract $reactable,
        string $typeName
    ): int {
        return $reactable
            ->getLoveReactant()
            ->getReactionCounterOfType(ReactionType::fromName($typeName))
            ->getCount();
    }

    public static function getReactableReactionsWeightForTypeName(
        ReactableContract $reactable,
        string $typeName
    ): int {
        return $reactable
            ->getLoveReactant()
            ->getReactionCounterOfType(ReactionType::fromName($typeName))
            ->getWeight();
    }

    public static function getReactableReactionsTotalCount(
        ReactableContract $reactable
    ): int {
        return $reactable
            ->getLoveReactant()
            ->getReactionTotal()
            ->getCount();
    }

    public static function getReactableReactionsTotalWeight(
        ReactableContract $reactable
    ): int {
        return $reactable
            ->getLoveReactant()
            ->getReactionTotal()
            ->getWeight();
    }
}
