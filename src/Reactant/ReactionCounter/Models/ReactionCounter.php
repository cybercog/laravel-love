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

namespace Cog\Laravel\Love\Reactant\ReactionCounter\Models;

use Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter as ReactionCounterContract;
use Illuminate\Database\Eloquent\Model;

class ReactionCounter extends Model implements ReactionCounterContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'love_reactant_reaction_counters';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reaction_type_id',
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

    // TODO: Add `getReactionType()`?
    // TODO: Add `isReactionOfType(ReactionType)`

    public function getCount(): int
    {
        return $this->getAttribute('count') ?? 0;
    }
}
