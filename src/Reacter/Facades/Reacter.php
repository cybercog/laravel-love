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

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableInterface;
use Cog\Contracts\Love\Reacter\Facades\Reacter as ReacterFacadeInterface;
use Cog\Contracts\Love\Reacter\Models\Reacter as ReacterInterface;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;

final class Reacter implements
    ReacterFacadeInterface
{
    /**
     * @var ReacterInterface
     */
    private $reacter;

    public function __construct(ReacterInterface $reacter)
    {
        $this->reacter = $reacter;
    }

    /**
     * @return iterable|\Cog\Contracts\Love\Reaction\Models\Reaction[]
     */
    public function getReactions(): iterable
    {
        return $this->reacter->getReactions();
    }

    public function reactTo(
        ReactableInterface $reactable,
        string $reactionTypeName,
        ?float $rate = null
    ): void {
        $this->reacter->reactTo(
            $reactable->getLoveReactant(),
            ReactionType::fromName($reactionTypeName),
            $rate
        );
    }

    public function unreactTo(
        ReactableInterface $reactable,
        string $reactionTypeName
    ): void {
        $this->reacter->unreactTo(
            $reactable->getLoveReactant(),
            ReactionType::fromName($reactionTypeName)
        );
    }

    public function hasReactedTo(
        ReactableInterface $reactable,
        ?string $reactionTypeName = null,
        ?float $rate = null
    ): bool {
        $reactionType = $reactionTypeName === null ? null : ReactionType::fromName($reactionTypeName);

        return $this->reacter->hasReactedTo(
            $reactable->getLoveReactant(),
            $reactionType,
            $rate
        );
    }

    public function hasNotReactedTo(
        ReactableInterface $reactable,
        ?string $reactionTypeName = null,
        ?float $rate = null
    ): bool {
        $reactionType = $reactionTypeName === null ? null : ReactionType::fromName($reactionTypeName);

        return $this->reacter->hasNotReactedTo(
            $reactable->getLoveReactant(),
            $reactionType,
            $rate
        );
    }
}
