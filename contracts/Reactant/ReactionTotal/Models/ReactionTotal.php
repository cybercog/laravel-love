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

namespace Cog\Contracts\Love\Reactant\ReactionTotal\Models;

use Cog\Contracts\Love\Reactant\Models\Reactant;

interface ReactionTotal
{
    public function getReactant(): Reactant;

    public function getCount(): int;

    public function getWeight(): int;

    public function incrementCount(int $amount): void;

    public function decrementCount(int $amount): void;

    public function incrementWeight(int $amount): void;

    public function decrementWeight(int $amount): void;
}
