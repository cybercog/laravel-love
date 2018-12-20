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

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reactant\ReactionCounter\Models\ReactionCounter as ReactionCounterContract;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeContract;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use TypeError;

final class ReactionCounter extends Model implements ReactionCounterContract
{
    protected $table = 'love_reactant_reaction_counters';

    protected $fillable = [
        'reaction_type_id',
        'count',
        'weight',
    ];

    protected $casts = [
        'count' => 'integer',
        'weight' => 'integer',
    ];

    public function reactant(): BelongsTo
    {
        return $this->belongsTo(Reactant::class, 'reactant_id');
    }

    public function reactionType(): BelongsTo
    {
        return $this->belongsTo(ReactionType::class, 'reaction_type_id');
    }

    public function getReactant(): ReactantContract
    {
        $reactant = $this->getAttribute('reactant');

        if (is_null($reactant)) {
            throw new TypeError();
        }

        return $reactant;
    }

    public function getReactionType(): ReactionTypeContract
    {
        $reactionType = $this->getAttribute('reactionType');

        if (is_null($reactionType)) {
            throw new TypeError();
        }

        return $reactionType;
    }

    public function isReactionOfType(ReactionTypeContract $reactionType): bool
    {
        return $this->getReactionType()->isEqualTo($reactionType);
    }

    public function isNotReactionOfType(ReactionTypeContract $reactionType): bool
    {
        return !$this->isReactionOfType($reactionType);
    }

    public function getCount(): int
    {
        return $this->getAttribute('count') ?? 0;
    }

    public function getWeight(): int
    {
        return $this->getAttribute('weight') ?? 0;
    }

    public function incrementCount(int $amount): void
    {
        $this->increment('count', $amount);
    }

    public function incrementWeight(int $amount): void
    {
        $this->increment('weight', $amount);
    }
}
