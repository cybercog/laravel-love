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

namespace Cog\Laravel\Love\Reaction\Events;

use Cog\Contracts\Love\Reaction\Models\Reaction as ReactionContract;

final class ReactionHasBeenAdded
{
    /**
     * @var ReactionContract
     */
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
