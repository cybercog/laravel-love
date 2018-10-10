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

namespace Cog\Laravel\Love\ReactionType\Models;

use Cog\Contracts\Love\ReactionType\Exceptions\ReactionTypeInvalid;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeContract;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReactionType extends Model implements ReactionTypeContract
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'love_reaction_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'weight',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'weight' => 'integer',
    ];

    private static $instances = [];

    protected static function boot(): void
    {
        parent::boot();

        static::saved(function (ReactionTypeContract $reactionType) {
            static::$instances[$reactionType->getName()] = $reactionType;
        });

        static::deleted(function (ReactionTypeContract $reactionType) {
            unset(static::$instances[$reactionType->getName()]);
        });
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'reaction_type_id');
    }

    public static function fromName(string $name): ReactionTypeContract
    {
        if (isset(static::$instances[$name])) {
            return static::$instances[$name];
        }

        /** @var \Cog\Laravel\Love\ReactionType\Models\ReactionType $type */
        $type = static::query()->where('name', $name)->first();

        if (!$type) {
            throw ReactionTypeInvalid::notExists($name);
        }

        static::$instances[$name] = $type;

        return $type;
    }

    public function getName(): string
    {
        return $this->getAttribute('name') ?? '';
    }

    public function getWeight(): int
    {
        return $this->getAttribute('weight') ?? 0;
    }

    public function isEqualTo(ReactionTypeContract $type): bool
    {
        return $type->getKey() === $this->getKey();
    }

    public function isNotEqualTo(ReactionTypeContract $type): bool
    {
        return !$this->isEqualTo($type);
    }
}
