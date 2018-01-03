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

namespace Cog\Contracts\Love\LikeCounter\Models;

/**
 * Interface LikeCounter.
 *
 * @property int type_id
 * @property int count
 * @package Cog\Contracts\Love\LikeCounter\Models
 */
interface LikeCounter
{
    /**
     * Likeable model relation.
     *
     * @return mixed
     */
    public function likeable();
}
