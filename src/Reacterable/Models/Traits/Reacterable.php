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

namespace Cog\Laravel\Love\Reacterable\Models\Traits;

use Cog\Contracts\Love\Reacter\Models\Reacter as ReacterContract;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait Reacterable.
 *
 * @mixin \Illuminate\Database\Eloquent\Model
 * @package Cog\Laravel\Love\Reacterable\Models\Traits
 */
trait Reacterable
{
    public function reacter(): BelongsTo
    {
        return $this->belongsTo(Reacter::class, 'love_reacter_id');
    }

    public function getReacter(): ReacterContract
    {
        // TODO: Return `NullReacter` if not set?
        return $this->getAttribute('reacter');
    }
}
