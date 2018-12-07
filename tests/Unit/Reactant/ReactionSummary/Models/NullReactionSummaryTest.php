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

use Cog\Laravel\Love\Reactant\Models\NullReactant;
use Cog\Laravel\Love\Reactant\ReactionSummary\Models\NullReactionSummary;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NullReactionSummaryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_total_count(): void
    {
        $reactable = new Article();
        $reactant = new NullReactant($reactable);
        $summary = new NullReactionSummary($reactant);

        $totalCount = $summary->getTotalCount();

        $this->assertSame(0, $totalCount);
    }

    /** @test */
    public function it_can_get_total_weight(): void
    {
        $reactable = new Article();
        $reactant = new NullReactant($reactable);
        $summary = new NullReactionSummary($reactant);

        $totalWeight = $summary->getTotalWeight();

        $this->assertSame(0, $totalWeight);
    }

    /** @test */
    public function it_can_get_reactant(): void
    {
        $reactable = new Article();
        $reactant = new NullReactant($reactable);
        $summary = new NullReactionSummary($reactant);

        $assertReactant = $summary->getReactant();

        $this->assertSame($reactant, $assertReactant);
    }
}
