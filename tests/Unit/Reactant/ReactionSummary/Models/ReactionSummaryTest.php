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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\ReactionSummary\Models;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionSummary\Models\ReactionSummary;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReactionSummaryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_fill_total_count(): void
    {
        $summary = new ReactionSummary([
            'total_count' => 4,
        ]);

        $this->assertSame(4, $summary->getAttribute('total_count'));
    }

    /** @test */
    public function it_can_fill_total_weight(): void
    {
        $summary = new ReactionSummary([
            'total_weight' => 4,
        ]);

        $this->assertSame(4, $summary->getAttribute('total_weight'));
    }

    /** @test */
    public function it_casts_total_count_to_integer(): void
    {
        $summary = new ReactionSummary([
            'total_count' => '4',
        ]);

        $this->assertSame(4, $summary->getAttribute('total_count'));
    }

    /** @test */
    public function it_casts_total_weight_to_integer(): void
    {
        $summary = new ReactionSummary([
            'total_weight' => '4',
        ]);

        $this->assertSame(4, $summary->getAttribute('total_weight'));
    }

    /** @test */
    public function it_can_belong_to_reactant(): void
    {
        $reactant = factory(Reactant::class)->create();

        $summary = factory(ReactionSummary::class)->create([
            'reactant_id' => $reactant->getKey(),
        ]);

        $this->assertTrue($summary->reactant->is($reactant));
    }

    /** @test */
    public function it_can_get_reactant(): void
    {
        $reactant = factory(Reactant::class)->create();

        $summary = factory(ReactionSummary::class)->create([
            'reactant_id' => $reactant->getKey(),
        ]);

        $this->assertTrue($summary->getReactant()->is($reactant));
    }

    /** @test */
    public function it_can_get_total_count(): void
    {
        $summary = new ReactionSummary([
            'total_count' => '4',
        ]);

        $this->assertSame(4, $summary->getTotalCount());
    }

    /** @test */
    public function it_can_get_total_count_if_not_set(): void
    {
        $summary = new ReactionSummary();

        $this->assertSame(0, $summary->getTotalCount());
    }

    /** @test */
    public function it_can_get_total_weight(): void
    {
        $summary = new ReactionSummary([
            'total_weight' => '4',
        ]);

        $this->assertSame(4, $summary->getTotalWeight());
    }

    /** @test */
    public function it_can_get_total_weight_if_not_set(): void
    {
        $summary = new ReactionSummary();

        $this->assertSame(0, $summary->getTotalWeight());
    }
}
