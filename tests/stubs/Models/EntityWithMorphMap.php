<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) CyberCog <support@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Likeable\Tests\Stubs\Models;

use Cog\Likeable\Contracts\HasLikes as HasLikesContract;
use Cog\Likeable\Traits\HasLikes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EntityWithMorphMap.
 *
 * @package Cog\Likeable\Tests\Stubs\Models
 */
class EntityWithMorphMap extends Model implements HasLikesContract
{
    use HasLikes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'entity_with_morph_map';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];
}
