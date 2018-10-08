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

namespace Cog\Contracts\Love\Reactant\ReactionCounter\Models;

use Cog\Contracts\Love\ReactionType\Models\ReactionType;

interface ReactionCounter
{
    public function getReactionType(): ReactionType;

    public function isReactionOfType(ReactionType $reactionType): bool;

    public function isNotReactionOfType(ReactionType $reactionType): bool;

    public function getCount(): int;
}
