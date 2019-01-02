<?php

/*
 * This file is part of Laravel Love.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cog\Laravel\Love\Reactant\ReactionTotal\Models;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reactant\ReactionTotal\Exceptions\ReactionTotalInvalid;
use Cog\Contracts\Love\Reactant\ReactionTotal\Models\ReactionTotal as ReactionTotalContract;

final class NullReactionTotal implements
    ReactionTotalContract
{
    private $reactant;

    public function __construct(
        ReactantContract $reactant
    ) {
        $this->reactant = $reactant;
    }

    public function getReactant(): ReactantContract
    {
        return $this->reactant;
    }

    public function getCount(): int
    {
        return 0;
    }

    public function getWeight(): int
    {
        return 0;
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
        int $amount
    ): void {
        throw ReactionTotalInvalid::notExists();
    }

    public function decrementWeight(
        int $amount
    ): void {
        throw ReactionTotalInvalid::notExists();
    }
}
