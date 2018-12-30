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

namespace Cog\Tests\Laravel\Love\Stubs\Models;

use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableContract;
use Cog\Laravel\Love\Reacterable\Models\Traits\Reacterable;
use Illuminate\Database\Eloquent\Model;

final class Bot extends Model implements
    ReacterableContract
{
    use Reacterable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bots';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];
}
