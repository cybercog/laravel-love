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
 * Class ModelWasUndisliked.
 *
 * @package Cog\Laravel\Love\Likeable\Events
 */
class ModelWasUndisliked
{
    /**
     * The undisliked model.
     *
     * @var \Cog\Contracts\Love\Likeable\Models\Likeable
     */
    public $model;

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
        $this->model = $likeable;
        $this->likerId = $likerId;
    }
}
