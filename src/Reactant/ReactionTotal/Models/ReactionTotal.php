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

namespace Cog\Laravel\Love\Reactant\ReactionTotal\Models;

use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
use Cog\Contracts\Love\Reactant\ReactionTotal\Models\ReactionTotal as ReactionTotalContract;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ReactionTotal extends Model implements ReactionTotalContract
{
    protected $table = 'love_reactant_reaction_totals';

    protected $fillable = [
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

    public function getReactant(): ReactantContract
    {
        return $this->getAttribute('reactant');
    }

    public function getCount(): int
    {
        return $this->getAttribute('count') ?? 0;
    }

    public function getWeight(): int
    {
        return $this->getAttribute('weight') ?? 0;
    }
}
