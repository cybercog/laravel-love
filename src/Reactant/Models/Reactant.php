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

namespace Cog\Laravel\Love\Reactant\Models;

use Cog\Laravel\Love\Reaction\Models\Reaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reactant extends Model
{
    protected $table = 'love_reactants';

    public function reactable(): MorphTo
    {
        return $this->morphTo('reactable', 'type', 'id', 'love_reactant_id');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'reactant_id');
    }
}
