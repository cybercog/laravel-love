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
use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableContract;
use Cog\Laravel\Love\Reacter\Models\NullReacter;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin \Cog\Contracts\Love\Reacterable\Models\Reacterable
 */
trait Reacterable
{
    protected static function bootReacterable(): void
    {
        static::creating(function (ReacterableContract $reacterable) {
            if ($reacterable->isNotRegisteredAsReacter()) {
                $reacter = Reacter::query()->create([
                    'type' => $reacterable->getMorphClass(),
                ]);

                $reacterable->setAttribute('love_reacter_id', $reacter->getKey());
            }
        });
    }

    public function reacter(): BelongsTo
    {
        return $this->belongsTo(Reacter::class, 'love_reacter_id');
    }

    public function getReacter(): ReacterContract
    {
        return $this->getAttribute('reacter') ?? new NullReacter($this);
    }

    public function isRegisteredAsReacter(): bool
    {
        return !$this->isNotRegisteredAsReacter();
    }

    public function isNotRegisteredAsReacter(): bool
    {
        return is_null($this->getAttribute('love_reacter_id'));
    }
}
