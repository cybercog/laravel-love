<?php

/*
 * This file is part of PHP Contracts: Love.
 *
 * (c) Anton Komarev <anton@komarev.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cog\Contracts\Love\Reactant\ReactionCounter\Models;

use Cog\Contracts\Love\Reactant\Models\Reactant;
use Cog\Contracts\Love\ReactionType\Models\ReactionType;

interface ReactionCounter
{
    public function getReactant(): Reactant;

    public function getReactionType(): ReactionType;

    public function isReactionOfType(ReactionType $reactionType): bool;

    public function isNotReactionOfType(ReactionType $reactionType): bool;

    public function getCount(): int;

    public function getWeight(): float;

    public function incrementCount(int $amount): void;

    public function decrementCount(int $amount): void;

    public function incrementWeight(float $amount): void;

    public function decrementWeight(float $amount): void;
}
