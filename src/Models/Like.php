<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Laravel\Likeable\Models;

use Cog\Contracts\Likeable\Like as LikeContract;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Like.
 *
 * @property \Cog\Contracts\Likeable\Likeable likeable
 * @property int type_id
 * @property int user_id
 * @package Cog\Laravel\Likeable\Models
 */
class Like extends Model implements LikeContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'likes';

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
     * Set the value of the "updated at" attribute.
     *
     * @todo drop it in 4.0 by adding updated_at column
     * @deprecated 3.0
     * @param  mixed  $value
     * @return $this
     */
    public function setUpdatedAt($value)
    {
        return $this;
    }

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
