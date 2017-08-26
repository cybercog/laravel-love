<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Likeable\Events;

use Cog\Likeable\Contracts\Likeable as LikeableContract;

/**
 * Class ModelWasUnliked.
 *
 * @package Cog\Likeable\Events
 */
class ModelWasUnliked
{
    /**
     * The unliked model.
     *
     * @var \Cog\Likeable\Contracts\Likeable
     */
    public $model;

    /**
     * User id who unliked model.
     *
     * @var int
     */
    public $likerId;

    /**
     * Create a new event instance.
     *
     * @param \Cog\Likeable\Contracts\Likeable $likeable
     * @param int $likerId
     * @return void
     */
    public function __construct(LikeableContract $likeable, $likerId)
    {
        $this->model = $likeable;
        $this->likerId = $likerId;
    }
}
