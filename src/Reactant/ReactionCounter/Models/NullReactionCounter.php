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

namespace Cog\Laravel\Love\Reactant\ReactionCounter\Models;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantInterface;
use Cog\Contracts\Love\Reactant\ReactionCounter\Exceptions\ReactionCounterInvalid;
use Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter as ReactionCounterInterface;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeInterface;

final class NullReactionCounter implements
    ReactionCounterInterface
{
    /**
     * @var ReactantInterface
     */
    private $reactant;

    /**
     * @var ReactionTypeInterface
     */
    private $reactionType;

    public function __construct(
        ReactantInterface $reactant,
        ReactionTypeInterface $reactionType
    ) {
        $this->reactant = $reactant;
        $this->reactionType = $reactionType;
    }

    public function getReactant(): ReactantInterface
    {
        return $this->reactant;
    }

    public function getReactionType(): ReactionTypeInterface
    {
        return $this->reactionType;
    }

    public function isReactionOfType(
        ReactionTypeInterface $reactionType
    ): bool {
        return $this->getReactionType()->isEqualTo($reactionType);
    }

    public function isNotReactionOfType(
        ReactionTypeInterface $reactionType
    ): bool {
        return !$this->isReactionOfType($reactionType);
    }

    public function getCount(): int
    {
        return 0;
    }

    public function getWeight(): float
    {
        return 0.0;
    }

    public function incrementCount(
        int $amount
    ): void {
        throw ReactionCounterInvalid::notExists();
    }

    public function decrementCount(
        int $amount
    ): void {
        throw ReactionCounterInvalid::notExists();
    }

    public function incrementWeight(
        float $amount
    ): void {
        throw ReactionCounterInvalid::notExists();
    }

    public function decrementWeight(
        float $amount
    ): void {
        throw ReactionCounterInvalid::notExists();
    }
}
