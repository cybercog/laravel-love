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

use Cog\Laravel\Love\Reactant\Models\NullReactant;
use Cog\Laravel\Love\Reactant\ReactionTotality\Models\NullReactionTotality;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NullReactionTotalityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_total_count(): void
    {
        $reactable = new Article();
        $reactant = new NullReactant($reactable);
        $totality = new NullReactionTotality($reactant);

        $totalCount = $totality->getTotalCount();

        $this->assertSame(0, $totalCount);
    }

    /** @test */
    public function it_can_get_total_weight(): void
    {
        $reactable = new Article();
        $reactant = new NullReactant($reactable);
        $totality = new NullReactionTotality($reactant);

        $totalWeight = $totality->getTotalWeight();

        $this->assertSame(0, $totalWeight);
    }

    /** @test */
    public function it_can_get_reactant(): void
    {
        $reactable = new Article();
        $reactant = new NullReactant($reactable);
        $totality = new NullReactionTotality($reactant);

        $assertReactant = $totality->getReactant();

        $this->assertSame($reactant, $assertReactant);
    }
}
