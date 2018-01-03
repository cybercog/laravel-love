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

namespace Cog\Laravel\Love\Likeable\Events;

use Cog\Contracts\Love\Likeable\Models\Likeable as LikeableContract;

/**
 * Class LikeableWasUndisliked.
 *
 * @package Cog\Laravel\Love\Likeable\Events
 */
class LikeableWasUndisliked
{
    /**
     * The undisliked likeable model.
     *
     * @var \Cog\Contracts\Love\Likeable\Models\Likeable
     */
    public $likeable;

    /**
     * User id who undisliked model.
     *
     * @var int
     */
    public $likerId;

    /**
     * Create a new event instance.
     *
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @param int $likerId
     * @return void
     */
    public function __construct(LikeableContract $likeable, $likerId)
    {
        $this->likeable = $likeable;
        $this->likerId = $likerId;
    }
}
