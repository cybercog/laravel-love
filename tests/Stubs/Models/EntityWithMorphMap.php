<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Tests\Laravel\Likeable\Stubs\Models;

use Cog\Contracts\Likeable\Likeable as LikeableContract;
use Cog\Laravel\Likeable\Traits\Likeable;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EntityWithMorphMap.
 *
 * @package Cog\Tests\Laravel\Likeable\Stubs\Models
 */
class EntityWithMorphMap extends Model implements LikeableContract
{
    use Likeable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'entities_with_morph_map';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];
}
