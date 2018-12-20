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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\ReactionTotal\Models;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\ReactionTotal;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class ReactionTotalTest extends TestCase
{
    use RefreshDatabase;

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
}
