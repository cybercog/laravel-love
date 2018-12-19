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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\ReactionTotality\Models;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionTotality\Models\ReactionTotality;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class ReactionTotalityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_fill_total_count(): void
    {
        $totality = new ReactionTotality([
            'total_count' => 4,
        ]);

        $this->assertSame(4, $totality->getAttribute('total_count'));
    }

    /** @test */
    public function it_can_fill_total_weight(): void
    {
        $totality = new ReactionTotality([
            'total_weight' => 4,
        ]);

        $this->assertSame(4, $totality->getAttribute('total_weight'));
    }

    /** @test */
    public function it_casts_total_count_to_integer(): void
    {
        $totality = new ReactionTotality([
            'total_count' => '4',
        ]);

        $this->assertSame(4, $totality->getAttribute('total_count'));
    }

    /** @test */
    public function it_casts_total_weight_to_integer(): void
    {
        $totality = new ReactionTotality([
            'total_weight' => '4',
        ]);

        $this->assertSame(4, $totality->getAttribute('total_weight'));
    }

    /** @test */
    public function it_can_belong_to_reactant(): void
    {
        $reactant = factory(Reactant::class)->create();

        $totality = factory(ReactionTotality::class)->create([
            'reactant_id' => $reactant->getKey(),
        ]);

        $this->assertTrue($totality->reactant->is($reactant));
    }

    /** @test */
    public function it_can_get_reactant(): void
    {
        $reactant = factory(Reactant::class)->create();

        $totality = factory(ReactionTotality::class)->create([
            'reactant_id' => $reactant->getKey(),
        ]);

        $this->assertTrue($totality->getReactant()->is($reactant));
    }

    /** @test */
    public function it_can_get_total_count(): void
    {
        $totality = new ReactionTotality([
            'total_count' => '4',
        ]);

        $this->assertSame(4, $totality->getTotalCount());
    }

    /** @test */
    public function it_can_get_total_count_if_not_set(): void
    {
        $totality = new ReactionTotality();

        $this->assertSame(0, $totality->getTotalCount());
    }

    /** @test */
    public function it_can_get_total_weight(): void
    {
        $totality = new ReactionTotality([
            'total_weight' => '4',
        ]);

        $this->assertSame(4, $totality->getTotalWeight());
    }

    /** @test */
    public function it_can_get_total_weight_if_not_set(): void
    {
        $totality = new ReactionTotality();

        $this->assertSame(0, $totality->getTotalWeight());
    }
}
