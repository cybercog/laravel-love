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
}
