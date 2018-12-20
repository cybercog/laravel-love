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

namespace Cog\Contracts\Love\Reaction\Models;

use Cog\Contracts\Love\Reactant\Models\Reactant;
use Cog\Contracts\Love\Reacter\Models\Reacter;
use Cog\Contracts\Love\ReactionType\Models\ReactionType;

interface Reaction
{
    public function getType(): ReactionType;

    public function getReactant(): Reactant;

    public function getReacter(): Reacter;

    public function getWeight(): int;

    public function isOfType(ReactionType $reactionType): bool;

    public function isNotOfType(ReactionType $reactionType): bool;

    public function isToReactant(Reactant $reactant): bool;

    public function isNotToReactant(Reactant $reactant): bool;

    public function isByReacter(Reacter $reacter): bool;

    public function isNotByReacter(Reacter $reacter): bool;
}
