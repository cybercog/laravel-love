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

namespace Cog\Laravel\Love\Reacter\Facades;

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Cog\Contracts\Love\Reacter\Facades\Reacter as ReacterFacadeContract;
use Cog\Contracts\Love\Reacter\Models\Reacter as ReacterContract;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;

final class Reacter implements ReacterFacadeContract
{
    private $reacter;

    public function __construct(ReacterContract $reacter)
    {
        $this->reacter = $reacter;
    }

    public function getReactions(): iterable
    {
        return $this->reacter->getReactions();
    }

    public function reactTo(
        ReactableContract $reactable,
        string $reactionTypeName
    ): void {
        $this->reacter->reactTo(
            $reactable->getLoveReactant(),
            ReactionType::fromName($reactionTypeName)
        );
    }

    public function unreactTo(
        ReactableContract $reactable,
        string $reactionTypeName
    ): void {
        $this->reacter->unreactTo(
            $reactable->getLoveReactant(),
            ReactionType::fromName($reactionTypeName)
        );
    }

    public function isReactedTo(
        ReactableContract $reactable
    ): bool {
        return $this->reacter->isReactedTo(
            $reactable->getLoveReactant()
        );
    }

    public function isNotReactedTo(
        ReactableContract $reactable
    ): bool {
        return $this->reacter->isNotReactedTo(
            $reactable->getLoveReactant()
        );
    }

    public function isReactedToWithType(
        ReactableContract $reactable,
        string $reactionTypeName
    ): bool {
        return $this->reacter->isReactedToWithType(
            $reactable->getLoveReactant(),
            ReactionType::fromName($reactionTypeName)
        );
    }

    public function isNotReactedToWithType(
        ReactableContract $reactable,
        string $reactionTypeName
    ): bool {
        return $this->reacter->isNotReactedToWithType(
            $reactable->getLoveReactant(),
            ReactionType::fromName($reactionTypeName)
        );
    }
}
