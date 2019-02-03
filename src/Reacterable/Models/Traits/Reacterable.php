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
use Cog\Contracts\Love\Reacterable\Exceptions\AlreadyRegisteredAsLoveReacter;
use Cog\Laravel\Love\Reacter\Models\NullReacter;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\Reacterable\Observers\ReacterableObserver;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin \Cog\Contracts\Love\Reacterable\Models\Reacterable
 */
trait Reacterable
{
    protected static function bootReacterable(): void
    {
        static::observe(ReacterableObserver::class);
    }

    public function loveReacter(): BelongsTo
    {
        return $this->belongsTo(Reacter::class, 'love_reacter_id');
    }

    public function getLoveReacter(): ReacterContract
    {
        return $this->getAttribute('loveReacter') ?? new NullReacter($this);
    }

    public function isRegisteredAsLoveReacter(): bool
    {
        return !$this->isNotRegisteredAsLoveReacter();
    }

    public function isNotRegisteredAsLoveReacter(): bool
    {
        return is_null($this->getAttributeValue('love_reacter_id'));
    }

    public function registerAsLoveReacter(): void
    {
        if ($this->isRegisteredAsLoveReacter()) {
            throw new AlreadyRegisteredAsLoveReacter();
        }

        /** @var \Cog\Contracts\Love\Reacter\Models\Reacter $reacter */
        $reacter = $this->loveReacter()->create([
            'type' => $this->getMorphClass(),
        ]);

        $this->setAttribute('love_reacter_id', $reacter->getId());
        $this->save();
    }
}
