<?php

/*
 * This file is part of Laravel Love.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Laravel\Love\Likeable\Events;

use Cog\Contracts\Love\Likeable\Models\Likeable as LikeableContract;

/**
 * Class ModelWasUnliked.
 *
 * @package Cog\Laravel\Love\Likeable\Events
 */
class ModelWasUnliked
{
    /**
     * The unliked model.
     *
     * @var \Cog\Contracts\Love\Likeable\Models\Likeable
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
     * @param \Cog\Contracts\Love\Likeable\Models\Likeable $likeable
     * @param int $likerId
     * @return void
     */
    public function __construct(LikeableContract $likeable, $likerId)
    {
        $this->model = $likeable;
        $this->likerId = $likerId;
    }
}
