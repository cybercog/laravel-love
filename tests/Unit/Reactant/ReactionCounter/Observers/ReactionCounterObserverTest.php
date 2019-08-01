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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\ReactionCounter\Observers;

use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Tests\Laravel\Love\TestCase;

final class ReactionCounterObserverTest extends TestCase
{
    /** @test */
    public function it_sets_default_count_value_when_count_value_is_null(): void
    {
        $counter = factory(ReactionCounter::class)->create([
            'count' => null,
        ]);

        $this->assertSame(ReactionCounter::COUNT_DEFAULT, $counter->getCount());
    }

    /** @test */
    public function it_sets_default_weight_value_when_weight_value_is_null(): void
    {
        $counter = factory(ReactionCounter::class)->create([
            'weight' => null,
        ]);

        $this->assertSame(ReactionCounter::WEIGHT_DEFAULT, $counter->getWeight());
    }
}
