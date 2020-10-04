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

namespace Cog\Laravel\Love\ReactionType\Models;

use Cog\Contracts\Love\ReactionType\Exceptions\ReactionTypeInvalid;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeInterface;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\Support\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

final class ReactionType extends Model implements
    ReactionTypeInterface
{
    public const MASS_DEFAULT = 0;

    protected $table = 'love_reaction_types';

    /**
     * @var int[]
     */
    protected $attributes = [
        'mass' => self::MASS_DEFAULT,
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'mass',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'id' => 'string',
        'mass' => 'integer',
    ];

    /**
     * @var array<self>
     */
    private static $nameCache = [];

    protected static function boot(): void
    {
        parent::boot();

        self::saved(function (self $reactionType) {
            self::$nameCache[$reactionType->getName()] = $reactionType;
        });

        self::deleted(function (self $reactionType) {
            unset(self::$nameCache[$reactionType->getName()]);
        });
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'reaction_type_id');
    }

    public static function fromName(
        string $name
    ): ReactionTypeInterface {
        if (isset(self::$nameCache[$name])) {
            return self::$nameCache[$name];
        }

        /** @var \Cog\Laravel\Love\ReactionType\Models\ReactionType|null $type */
        $type = self::query()->where('name', $name)->first();

        if ($type === null) {
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

    public function getMass(): int
    {
        return $this->getAttributeValue('mass');
    }

    public function isEqualTo(
        ReactionTypeInterface $that
    ): bool {
        return $this->getId() === $that->getId();
    }

    public function isNotEqualTo(
        ReactionTypeInterface $that
    ): bool {
        return !$this->isEqualTo($that);
    }
}
