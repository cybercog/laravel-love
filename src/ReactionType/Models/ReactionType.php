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

use Cog\Laravel\Love\Reaction\Models\Reaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReactionType extends Model
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

    protected $casts = [
        'weight' => 'integer',
    ];

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class, 'reaction_type_id');
    }

    public static function fromName(string $name): self
    {
        /** @var \Cog\Laravel\Love\ReactionType\Models\ReactionType $type */
        $type = static::query()->where('name', $name)->first();

        if (!$type) {
            throw new \RuntimeException(
                sprintf('ReactionType with name `%s` not found.', $name)
            );
        }

        return $type;
    }
}
