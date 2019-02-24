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

namespace Cog\Laravel\Love\Reaction\Events;

use Cog\Contracts\Love\Reaction\Models\Reaction as ReactionContract;

final class ReactionHasBeenRemoved
{
    private $reaction;

    public function __construct(
        ReactionContract $reaction
    ) {
        $this->reaction = $reaction;
    }

    public function getReaction(): ReactionContract
    {
        return $this->reaction;
    }
}
