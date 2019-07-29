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

namespace Cog\Contracts\Love\Reaction\Models;

use Cog\Contracts\Love\Reactant\Models\Reactant;
use Cog\Contracts\Love\Reacter\Models\Reacter;
use Cog\Contracts\Love\ReactionType\Models\ReactionType;

interface Reaction
{
    public function getId(): string;

    public function getReactant(): Reactant;

    public function getReacter(): Reacter;

    public function getType(): ReactionType;

    public function getRate(): float;

    public function getWeight(): float;

    public function isOfType(ReactionType $type): bool;

    public function isNotOfType(ReactionType $type): bool;

    public function isToReactant(Reactant $reactant): bool;

    public function isNotToReactant(Reactant $reactant): bool;

    public function isByReacter(Reacter $reacter): bool;

    public function isNotByReacter(Reacter $reacter): bool;
}
