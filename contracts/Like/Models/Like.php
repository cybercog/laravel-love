<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Contracts\Likeable\Like\Models;

/**
 * Interface Like.
 *
 * @property \Cog\Contracts\Likeable\Likeable\Models\Likeable likeable
 * @property int type_id
 * @property int user_id
 * @package Cog\Contract\Likeable\Like\Models
 */
interface Like
{
    /**
     * Likeable model relation.
     *
     * @return mixed
     */
    public function likeable();
}
