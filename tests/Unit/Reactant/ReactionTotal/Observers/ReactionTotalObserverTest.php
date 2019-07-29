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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\ReactionTotal\Observers;

use Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal;
use Cog\Tests\Laravel\Love\TestCase;

final class ReactionTotalObserverTest extends TestCase
{
    /** @test */
    public function it_sets_default_count_value_when_count_value_is_null(): void
    {
        $total = factory(ReactionTotal::class)->create([
            'count' => null,
        ]);

        $this->assertSame(ReactionTotal::DEFAULT_COUNT, $total->getCount());
    }

    /** @test */
    public function it_sets_default_weight_value_when_weight_value_is_null(): void
    {
        $total = factory(ReactionTotal::class)->create([
            'weight' => null,
        ]);

        $this->assertSame(ReactionTotal::DEFAULT_WEIGHT, $total->getWeight());
    }
}
