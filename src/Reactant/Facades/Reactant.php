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

namespace Cog\Laravel\Love\Reactant\Facades;

use Cog\Contracts\Love\Reactant\Facades\Reactant as ReacterFacadeContract;
use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter as ReactionCounterContract;
use Cog\Contracts\Love\Reactant\ReactionTotal\Models\ReactionTotal as ReactionTotalContract;
use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableContract;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;

final class Reactant implements ReacterFacadeContract
{
    private $reactant;

    public function __construct(ReactantContract $reactant)
    {
        $this->reactant = $reactant;
    }

    public function getReactions(): iterable
    {
        return $this->reactant->getReactions();
    }

    public function getReactionCounters(): iterable
    {
        return $this->reactant->getReactionCounters();
    }

    public function getReactionCounterOfType(
        string $reactionTypeName
    ): ReactionCounterContract {
        return $this->reactant->getReactionCounterOfType(
            ReactionType::fromName($reactionTypeName)
        );
    }

    public function getReactionTotal(): ReactionTotalContract
    {
        return $this->reactant->getReactionTotal();
    }

    public function isReactedBy(
        ReacterableContract $reacterable
    ): bool {
        return $this->reactant->isReactedBy($reacterable->getLoveReacter());
    }

    public function isNotReactedBy(
        ReacterableContract $reacterable
    ): bool {
        return $this->reactant->isNotReactedBy($reacterable->getLoveReacter());
    }

    public function isReactedByWithType(
        ReacterableContract $reacterable,
        string $reactionTypeName
    ): bool {
        return $this->reactant->isReactedByWithType(
            $reacterable->getLoveReacter(),
            ReactionType::fromName($reactionTypeName)
        );
    }

    public function isNotReactedByWithType(
        ReacterableContract $reacterable,
        string $reactionTypeName
    ): bool {
        return $this->reactant->isNotReactedByWithType(
            $reacterable->getLoveReacter(),
            ReactionType::fromName($reactionTypeName)
        );
    }
}
