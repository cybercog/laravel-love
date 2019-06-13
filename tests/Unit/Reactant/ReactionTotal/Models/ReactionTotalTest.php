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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\ReactionTotal\Models;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal;
use Cog\Tests\Laravel\Love\TestCase;
use TypeError;

final class ReactionTotalTest extends TestCase
{
    /** @test */
    public function it_can_fill_count(): void
    {
        $total = new ReactionTotal([
            'count' => 4,
        ]);

        $this->assertSame(4, $total->getAttribute('count'));
    }

    /** @test */
    public function it_can_fill_weight(): void
    {
        $total = new ReactionTotal([
            'weight' => 4,
        ]);

        $this->assertSame(4, $total->getAttribute('weight'));
    }

    /** @test */
    public function it_casts_count_to_integer(): void
    {
        $total = new ReactionTotal([
            'count' => '4',
        ]);

        $this->assertSame(4, $total->getAttribute('count'));
    }

    /** @test */
    public function it_casts_weight_to_integer(): void
    {
        $total = new ReactionTotal([
            'weight' => '4',
        ]);

        $this->assertSame(4, $total->getAttribute('weight'));
    }

    /** @test */
    public function it_can_belong_to_reactant(): void
    {
        $reactant = factory(Reactant::class)->create();

        $total = factory(ReactionTotal::class)->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($total->reactant->is($reactant));
    }

    /** @test */
    public function it_can_get_reactant(): void
    {
        $reactant = factory(Reactant::class)->create();

        $total = factory(ReactionTotal::class)->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($total->getReactant()->is($reactant));
    }

    /** @test */
    public function it_throws_exception_on_get_reactant_when_reactant_is_null(): void
    {
        $this->expectException(TypeError::class);

        $total = new ReactionTotal();

        $total->getReactant();
    }

    /** @test */
    public function it_can_get_count(): void
    {
        $total = new ReactionTotal([
            'count' => '4',
        ]);

        $this->assertSame(4, $total->getCount());
    }

    /** @test */
    public function it_can_get_count_if_not_set(): void
    {
        $total = new ReactionTotal();

        $this->assertSame(0, $total->getCount());
    }

    /** @test */
    public function it_can_get_weight(): void
    {
        $total = new ReactionTotal([
            'weight' => '4',
        ]);

        $this->assertSame(4, $total->getWeight());
    }

    /** @test */
    public function it_can_get_weight_if_not_set(): void
    {
        $total = new ReactionTotal();

        $this->assertSame(0, $total->getWeight());
    }

    /** @test */
    public function it_can_increment_count(): void
    {
        $total = factory(ReactionTotal::class)->create([
            'count' => 0,
        ]);

        $total->incrementCount(2);

        $this->assertSame(2, $total->getCount());
    }

    /** @test */
    public function it_can_increment_count_many_times(): void
    {
        $total = factory(ReactionTotal::class)->create([
            'count' => 0,
        ]);

        $total->incrementCount(2);
        $total->incrementCount(3);

        $this->assertSame(5, $total->getCount());
    }

    /** @test */
    public function it_can_decrement_count(): void
    {
        $total = factory(ReactionTotal::class)->create([
            'count' => 10,
        ]);

        $total->decrementCount(2);

        $this->assertSame(8, $total->getCount());
    }

    /** @test */
    public function it_can_decrement_count_many_times(): void
    {
        $total = factory(ReactionTotal::class)->create([
            'count' => 10,
        ]);

        $total->decrementCount(2);
        $total->decrementCount(3);

        $this->assertSame(5, $total->getCount());
    }

    /** @test */
    public function it_can_increment_weight(): void
    {
        $total = factory(ReactionTotal::class)->create([
            'weight' => 0,
        ]);

        $total->incrementWeight(2);

        $this->assertSame(2, $total->getWeight());
    }

    /** @test */
    public function it_can_increment_weight_many_times(): void
    {
        $total = factory(ReactionTotal::class)->create([
            'weight' => 0,
        ]);

        $total->incrementWeight(2);
        $total->incrementWeight(3);

        $this->assertSame(5, $total->getWeight());
    }

    /** @test */
    public function it_can_decrement_weight(): void
    {
        $total = factory(ReactionTotal::class)->create([
            'weight' => 10,
        ]);

        $total->decrementWeight(2);

        $this->assertSame(8, $total->getWeight());
    }

    /** @test */
    public function it_can_decrement_weight_many_times(): void
    {
        $total = factory(ReactionTotal::class)->create([
            'weight' => 10,
        ]);

        $total->decrementWeight(2);
        $total->decrementWeight(3);

        $this->assertSame(5, $total->getWeight());
    }
}
