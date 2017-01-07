<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Likeable\Contracts;

/**
 * Interface Like.
 *
 * @property \Cog\Likeable\Contracts\HasLikes likeable
 * @property int type_id
 * @property int user_id
 * @package Cog\Likeable\Contract
 */
interface Like
{
    /**
     * Likeable model relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function likeable();
}
