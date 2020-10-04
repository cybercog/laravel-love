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

namespace Cog\Laravel\Love\Reactant\ReactionTotal\Models;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantInterface;
use Cog\Contracts\Love\Reactant\ReactionTotal\Exceptions\ReactionTotalInvalid;
use Cog\Contracts\Love\Reactant\ReactionTotal\Models\ReactionTotal as ReactionTotalInterface;

final class NullReactionTotal implements
    ReactionTotalInterface
{
    /**
     * @var ReactantInterface
     */
    private $reactant;

    public function __construct(
        ReactantInterface $reactant
    ) {
        $this->reactant = $reactant;
    }

    public function getReactant(): ReactantInterface
    {
        return $this->reactant;
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
        throw ReactionTotalInvalid::notExists();
    }

    public function decrementCount(
        int $amount
    ): void {
        throw ReactionTotalInvalid::notExists();
    }

    public function incrementWeight(
        float $amount
    ): void {
        throw ReactionTotalInvalid::notExists();
    }

    public function decrementWeight(
        float $amount
    ): void {
        throw ReactionTotalInvalid::notExists();
    }
}
