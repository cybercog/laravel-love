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

final class ReactionType extends Model implements ReactionTypeContract
{
    protected $table = 'love_reaction_types';

    protected $fillable = [
        'name',
        'weight',
    ];

    protected $casts = [
        'id' => 'string',
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

    public static function fromName(
        string $name
    ): ReactionTypeContract {
        if (isset(static::$instances[$name])) {
            return static::$instances[$name];
        }

        /** @var \Cog\Laravel\Love\ReactionType\Models\ReactionType $type */
        $type = static::query()->where('name', $name)->first();

        if (!$type) {
            throw ReactionTypeInvalid::nameNotExists($name);
        }

        static::$instances[$name] = $type;

        return $type;
    }

    public function getId(): string
    {
        return $this->getAttributeValue('id');
    }

    public function getName(): string
    {
        return $this->getAttributeValue('name');
    }

    public function getWeight(): int
    {
        return $this->getAttributeValue('weight') ?? 0;
    }

    public function isEqualTo(
        ReactionTypeContract $type
    ): bool {
        return $type->getId() === $this->getId();
    }

    public function isNotEqualTo(
        ReactionTypeContract $type
    ): bool {
        return !$this->isEqualTo($type);
    }
}
