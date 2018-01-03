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

namespace Cog\Laravel\Love\LikeCounter\Models;

use Cog\Contracts\Love\LikeCounter\Models\LikeCounter as LikeCounterContract;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LikeCounter.
 *
 * @property int type_id
 * @property int count
 * @package Cog\Laravel\Love\LikeCounter\Models
 */
class LikeCounter extends Model implements LikeCounterContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'love_like_counters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type_id',
        'count',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'count' => 'integer',
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
