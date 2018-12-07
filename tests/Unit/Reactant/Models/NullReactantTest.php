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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\Models;

use Cog\Laravel\Love\Reactant\Models\NullReactant;
use Cog\Laravel\Love\Reactant\ReactionSummary\Models\NullReactionSummary;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class NullReactantTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_reactant(): void
    {
        $reactable = new Article();
        $reactant = new NullReactant($reactable);

        $assertReactable = $reactant->getReactable();

        $this->assertSame($reactable, $assertReactable);
    }

    /** @test */
    public function it_can_get_reactions(): void
    {
        $reactable = new Article();
        $reactant = new NullReactant($reactable);

        $reactions = $reactant->getReactions();

        $this->assertCount(0, $reactions);
        $this->assertInternalType('iterable', $reactions);
    }

    /** @test */
    public function it_can_get_reaction_counters(): void
    {
        $reactable = new Article();
        $reactant = new NullReactant($reactable);

        $counters = $reactant->getReactionCounters();

        $this->assertCount(0, $counters);
        $this->assertInternalType('iterable', $counters);
    }

    /** @test */
    public function it_can_get_reaction_summary(): void
    {
        $reactable = new Article();
        $reactant = new NullReactant($reactable);

        $summary = $reactant->getReactionSummary();

        $this->assertInstanceOf(NullReactionSummary::class, $summary);
    }
}
