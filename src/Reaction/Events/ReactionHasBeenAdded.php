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

use Cog\Contracts\Love\Reaction\Models\Reaction as ReactionInterface;

final class ReactionHasBeenAdded
{
    /**
     * @var ReactionInterface
     */
    private $reaction;

    public function __construct(
        ReactionInterface $reaction
    ) {
        $this->reaction = $reaction;
    }

    public function getReaction(): ReactionInterface
    {
        return $this->reaction;
    }
}
