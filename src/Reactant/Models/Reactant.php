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

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reactant\ReactionSummary\Models\ReactionSummary as ReactionSummaryContract;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionSummary\Models\NullReactionSummary;
use Cog\Laravel\Love\Reactant\ReactionSummary\Models\ReactionSummary;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reactant extends Model implements ReactantContract
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

    public function reactionCounters(): HasMany
    {
        return $this->hasMany(ReactionCounter::class, 'reactant_id');
    }

    public function reactionSummary(): HasOne
    {
        return $this->hasOne(ReactionSummary::class, 'reactant_id');
    }

    public function getReactable(): ReactableContract
    {
        // TODO: Return `NullReactable` if not set?
        return $this->getAttribute('reactable');
    }

    public function getReactions(): iterable
    {
        return $this->getAttribute('reactions');
    }

    public function getReactionCounters(): iterable
    {
        return $this->getAttribute('reactionCounters');
    }

    public function getReactionSummary(): ReactionSummaryContract
    {
        return $this->getAttribute('reactionSummary') ?? new NullReactionSummary();
    }
}
