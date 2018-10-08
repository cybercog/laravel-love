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
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeContract;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function reactionType(): BelongsTo
    {
        return $this->belongsTo(ReactionType::class, 'reaction_type_id');
    }

    public function getReactionType(): ReactionTypeContract
    {
        return $this->getAttribute('reactionType');
    }

    public function isReactionOfType(ReactionTypeContract $reactionType): bool
    {
        return $reactionType->getKey() === $this->getReactionType()->getKey();
    }

    public function isNotReactionOfType(ReactionTypeContract $reactionType): bool
    {
        return !$this->isReactionOfType($reactionType);
    }

    public function getCount(): int
    {
        return $this->getAttribute('count') ?? 0;
    }
}
