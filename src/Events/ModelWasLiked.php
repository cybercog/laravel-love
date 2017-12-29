<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Laravel\Likeable\Events;

use Cog\Contracts\Likeable\Likeable as LikeableContract;

/**
 * Class ModelWasLiked.
 *
 * @package Cog\Laravel\Likeable\Events
 */
class ModelWasLiked
{
    /**
     * The liked model.
     *
     * @var \Cog\Contracts\Likeable\Likeable
     */
    public $model;

    /**
     * User id who liked model.
     *
     * @var int
     */
    public $likerId;

    /**
     * Create a new event instance.
     *
     * @param \Cog\Contracts\Likeable\Likeable $likeable
     * @param int $likerId
     * @return void
     */
    public function __construct(LikeableContract $likeable, $likerId)
    {
        $this->model = $likeable;
        $this->likerId = $likerId;
    }
}
