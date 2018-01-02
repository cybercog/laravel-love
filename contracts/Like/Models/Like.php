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

namespace Cog\Contracts\Love\Like\Models;

/**
 * Interface Like.
 *
 * @property \Cog\Contracts\Love\Likeable\Models\Likeable likeable
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
