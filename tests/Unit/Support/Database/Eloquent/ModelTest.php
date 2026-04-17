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

namespace Cog\Tests\Laravel\Love\Unit\Support\Database\Eloquent;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Database\Eloquent\Collection;

final class ModelTest extends TestCase
{
    /**
     * Regression test for Laravel 13 compatibility.
     *
     * Laravel 13's HasCollection::resolveCollectionFromAttribute() walks up
     * the class hierarchy with `new (get_parent_class(static::class))`.
     * If the parent class is abstract, this throws a fatal error.
     *
     * Fixed upstream in laravel/framework#59488 (Laravel 13.4.0).
     */
    public function test_new_collection_works_on_concrete_models(): void
    {
        $modelClasses = [
            Reactant::class,
            Reacter::class,
            Reaction::class,
            ReactionType::class,
            ReactionCounter::class,
            ReactionTotal::class,
        ];

        foreach ($modelClasses as $modelClass) {
            $collection = (new $modelClass())->newCollection([]);

            $this->assertInstanceOf(
                Collection::class,
                $collection,
                sprintf('%s::newCollection() did not return Eloquent Collection', $modelClass),
            );
        }
    }
}
