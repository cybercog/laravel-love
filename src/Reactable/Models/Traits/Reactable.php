<?php

/*
 * This file is part of Laravel Love.
 *
 * (c) Anton Komarev <anton@komarev.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cog\Laravel\Love\Reactable\Models\Traits;

use Cog\Contracts\Love\Reactable\Exceptions\AlreadyRegisteredAsLoveReactant;
use Cog\Contracts\Love\Reactant\Facades\Reactant as ReactantFacadeInterface;
use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantInterface;
use Cog\Laravel\Love\Reactable\Observers\ReactableObserver;
use Cog\Laravel\Love\Reactant\Facades\Reactant as ReactantFacade;
use Cog\Laravel\Love\Reactant\Models\NullReactant;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin \Cog\Contracts\Love\Reactable\Models\Reactable
 */
trait Reactable
{
    protected static function bootReactable(): void
    {
        static::observe(ReactableObserver::class);
    }

    public function loveReactant(): BelongsTo
    {
        return $this->belongsTo(Reactant::class, 'love_reactant_id');
    }

    public function getLoveReactant(): ReactantInterface
    {
        return $this->getAttribute('loveReactant') ?? new NullReactant($this);
    }

    public function viaLoveReactant(): ReactantFacadeInterface
    {
        return new ReactantFacade($this->getLoveReactant());
    }

    public function isRegisteredAsLoveReactant(): bool
    {
        return !$this->isNotRegisteredAsLoveReactant();
    }

    public function isNotRegisteredAsLoveReactant(): bool
    {
        return $this->getAttributeValue('love_reactant_id') === null;
    }

    public function registerAsLoveReactant(): void
    {
        if ($this->isRegisteredAsLoveReactant()) {
            throw new AlreadyRegisteredAsLoveReactant();
        }

        /** @var \Cog\Contracts\Love\Reactant\Models\Reactant $reactant */
        $reactant = $this->loveReactant()->create([
            'type' => $this->getMorphClass(),
        ]);

        $this->setAttribute('love_reactant_id', $reactant->getId());
        $this->save();
    }
}
