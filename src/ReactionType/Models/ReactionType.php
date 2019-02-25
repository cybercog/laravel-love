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

final class ReactionType extends Model implements
    ReactionTypeContract
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

    private static $nameCache = [];

    protected static function boot(): void
    {
        parent::boot();

        self::saved(function (ReactionTypeContract $reactionType) {
            self::$nameCache[$reactionType->getName()] = $reactionType;
        });

        self::deleted(function (ReactionTypeContract $reactionType) {
            unset(self::$nameCache[$reactionType->getName()]);
        });
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'reaction_type_id');
    }

    public static function fromName(
        string $name
    ): ReactionTypeContract {
        if (isset(self::$nameCache[$name])) {
            return self::$nameCache[$name];
        }

        /** @var \Cog\Laravel\Love\ReactionType\Models\ReactionType $type */
        $type = self::query()->where('name', $name)->first();

        if (is_null($type)) {
            throw ReactionTypeInvalid::nameNotExists($name);
        }

        self::$nameCache[$name] = $type;

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
        ReactionTypeContract $that
    ): bool {
        return $this->getId() === $that->getId();
    }

    public function isNotEqualTo(
        ReactionTypeContract $that
    ): bool {
        return !$this->isEqualTo($that);
    }
}
