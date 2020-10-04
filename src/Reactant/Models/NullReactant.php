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

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableInterface;
use Cog\Contracts\Love\Reactant\Exceptions\ReactantInvalid;
use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantInterface;
use Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter as ReactionCounterInterface;
use Cog\Contracts\Love\Reactant\ReactionTotal\Models\ReactionTotal as ReactionTotalInterface;
use Cog\Contracts\Love\Reacter\Models\Reacter as ReacterInterface;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeInterface;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\NullReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\NullReactionTotal;
use Illuminate\Support\Collection;
use TypeError;

final class NullReactant implements
    ReactantInterface
{
    /**
     * @var ReactableInterface
     */
    private $reactable;

    public function __construct(
        ReactableInterface $reactable
    ) {
        $this->reactable = $reactable;
    }

    public function getId(): string
    {
        throw new TypeError();
    }

    public function getReactable(): ReactableInterface
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
        ReactionTypeInterface $reactionType
    ): ReactionCounterInterface {
        return new NullReactionCounter($this, $reactionType);
    }

    public function getReactionTotal(): ReactionTotalInterface
    {
        return new NullReactionTotal($this);
    }

    public function isReactedBy(
        ReacterInterface $reacter,
        ?ReactionTypeInterface $reactionType = null,
        ?float $rate = null
    ): bool {
        return false;
    }

    public function isNotReactedBy(
        ReacterInterface $reacter,
        ?ReactionTypeInterface $reactionType = null,
        ?float $rate = null
    ): bool {
        return true;
    }

    public function isEqualTo(
        ReactantInterface $that
    ): bool {
        return $that instanceof self;
    }

    public function isNotEqualTo(
        ReactantInterface $that
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
        ReactionTypeInterface $reactionType
    ): void {
        throw ReactantInvalid::notExists();
    }

    public function createReactionTotal(): void
    {
        throw ReactantInvalid::notExists();
    }
}
