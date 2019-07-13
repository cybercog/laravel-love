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

namespace Cog\Laravel\Love\Reactant\Models;

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Cog\Contracts\Love\Reactant\Exceptions\ReactantInvalid;
use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter as ReactionCounterContract;
use Cog\Contracts\Love\Reactant\ReactionTotal\Models\ReactionTotal as ReactionTotalContract;
use Cog\Contracts\Love\Reacter\Models\Reacter as ReacterContract;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeContract;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\NullReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\NullReactionTotal;
use Illuminate\Support\Collection;
use TypeError;

final class NullReactant implements
    ReactantContract
{
    private $reactable;

    public function __construct(
        ReactableContract $reactable
    ) {
        $this->reactable = $reactable;
    }

    public function getId(): string
    {
        throw new TypeError();
    }

    public function getReactable(): ReactableContract
    {
        return $this->reactable;
    }

    public function getReactions(): iterable
    {
        return new Collection();
    }

    public function getReactionCounters(): iterable
    {
        return new Collection();
    }

    public function getReactionCounterOfType(
        ReactionTypeContract $reactionType
    ): ReactionCounterContract {
        return new NullReactionCounter($this, $reactionType);
    }

    public function getReactionTotal(): ReactionTotalContract
    {
        return new NullReactionTotal($this);
    }

    public function isReactedBy(
        ReacterContract $reacter,
        ?ReactionTypeContract $reactionType = null
    ): bool {
        return false;
    }

    public function isNotReactedBy(
        ReacterContract $reacter,
        ?ReactionTypeContract $reactionType = null
    ): bool {
        return true;
    }

    public function isEqualTo(
        ReactantContract $that
    ): bool {
        return $that instanceof self;
    }

    public function isNotEqualTo(
        ReactantContract $that
    ): bool {
        return !$this->isEqualTo($that);
    }

    public function isNull(): bool
    {
        return true;
    }

    public function isNotNull(): bool
    {
        return false;
    }

    public function createReactionCounterOfType(
        ReactionTypeContract $reactionType
    ): void {
        throw ReactantInvalid::notExists();
    }

    public function createReactionTotal(): void
    {
        throw ReactantInvalid::notExists();
    }
}
