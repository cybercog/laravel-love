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

namespace Cog\Laravel\Love\Like\Models;

use Cog\Contracts\Love\Like\Models\Like as LikeContract;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Like.
 *
 * @property \Cog\Contracts\Love\Likeable\Models\Likeable likeable
 * @property int type_id
 * @property int user_id
 * @package Cog\Laravel\Love\Like\Models
 */
class Like extends Model implements LikeContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'love_likes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'type_id',
    ];

    /**
     * Likeable model relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function likeable()
    {
        return $this->morphTo();
    }
}
