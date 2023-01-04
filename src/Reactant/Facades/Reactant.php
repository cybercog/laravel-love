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

use Cog\Contracts\Love\Reactant\Facades\Reactant as ReacterFacadeInterface;
use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantInterface;
use Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter as ReactionCounterInterface;
use Cog\Contracts\Love\Reactant\ReactionTotal\Models\ReactionTotal as ReactionTotalInterface;
use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableInterface;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;

final class Reactant implements
    ReacterFacadeInterface
{
    private ReactantInterface $reactant;

    public function __construct(
        ReactantInterface $reactant,
    ) {
        $this->reactant = $reactant;
    }

    /**
     * @return iterable|\Cog\Contracts\Love\Reaction\Models\Reaction[]
     */
    public function getReactions(): iterable
    {
        return $this->reactant->getReactions();
    }

    /**
     * @return iterable|\Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter[]
     */
    public function getReactionCounters(): iterable
    {
        return $this->reactant->getReactionCounters();
    }

    public function getReactionCounterOfType(
        string $reactionTypeName,
    ): ReactionCounterInterface {
        return $this->reactant->getReactionCounterOfType(
            ReactionType::fromName($reactionTypeName)
        );
    }

    public function getReactionTotal(): ReactionTotalInterface
    {
        return $this->reactant->getReactionTotal();
    }

    public function isReactedBy(
        ?ReacterableInterface $reacterable = null,
        ?string $reactionTypeName = null,
        ?float $rate = null,
    ): bool {
        if ($reacterable === null) {
            return false;
        }

        $reactionType = $reactionTypeName === null ? null : ReactionType::fromName($reactionTypeName);

        return $this->reactant->isReactedBy(
            $reacterable->getLoveReacter(),
            $reactionType,
            $rate
        );
    }

    public function isNotReactedBy(
        ?ReacterableInterface $reacterable = null,
        ?string $reactionTypeName = null,
        ?float $rate = null,
    ): bool {
        if ($reacterable === null) {
            return true;
        }

        $reactionType = $reactionTypeName === null ? null : ReactionType::fromName($reactionTypeName);

        return $this->reactant->isNotReactedBy(
            $reacterable->getLoveReacter(),
            $reactionType,
            $rate
        );
    }
}
