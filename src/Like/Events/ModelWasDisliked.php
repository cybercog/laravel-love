<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Laravel\Likeable\Like\Events;

use Cog\Contracts\Likeable\Likeable\Models\Likeable as LikeableContract;

/**
 * Class ModelWasDisliked.
 *
 * @package Cog\Laravel\Likeable\Like\Events
 */
class ModelWasDisliked
{
    /**
     * The disliked model.
     *
     * @var \Cog\Contracts\Likeable\Likeable\Models\Likeable
     */
    public $model;

    /**
     * User id who disliked model.
     *
     * @var int
     */
    public $likerId;

    /**
     * Create a new event instance.
     *
     * @param \Cog\Contracts\Likeable\Likeable\Models\Likeable $likeable
     * @param int $likerId
     * @return void
     */
    public function __construct(LikeableContract $likeable, $likerId)
    {
        $this->model = $likeable;
        $this->likerId = $likerId;
    }
}
