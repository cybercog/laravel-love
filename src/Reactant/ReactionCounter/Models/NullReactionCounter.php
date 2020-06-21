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

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reactant\ReactionCounter\Exceptions\ReactionCounterInvalid;
use Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter as ReactionCounterContract;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeContract;

final class NullReactionCounter implements
    ReactionCounterContract
{
    /**
     * @var ReactantContract
     */
    private $reactant;

    /**
     * @var ReactionTypeContract
     */
    private $reactionType;

    public function __construct(
        ReactantContract $reactant,
        ReactionTypeContract $reactionType
    ) {
        $this->reactant = $reactant;
        $this->reactionType = $reactionType;
    }

    public function getReactant(): ReactantContract
    {
        return $this->reactant;
    }

    public function getReactionType(): ReactionTypeContract
    {
        return $this->reactionType;
    }

    public function isReactionOfType(
        ReactionTypeContract $reactionType
    ): bool {
        return $this->getReactionType()->isEqualTo($reactionType);
    }

    public function isNotReactionOfType(
        ReactionTypeContract $reactionType
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
