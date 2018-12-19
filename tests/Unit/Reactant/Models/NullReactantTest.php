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
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\NullReactionTotal;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
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
    public function it_can_get_reaction_total(): void
    {
        $reactable = new Article();
        $reactant = new NullReactant($reactable);

        $total = $reactant->getReactionTotal();

        $this->assertInstanceOf(NullReactionTotal::class, $total);
    }

    /** @test */
    public function it_can_determine_is_reacted_by(): void
    {
        $reactable = new Article();
        $reactant = new NullReactant($reactable);
        $reacter = factory(Reacter::class)->make();

        $isReacted = $reactant->isReactedBy($reacter);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_determine_is_not_reacted_by(): void
    {
        $reactable = new Article();
        $reactant = new NullReactant($reactable);
        $reacter = factory(Reacter::class)->make();

        $isReacted = $reactant->isNotReactedBy($reacter);

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_determine_is_reacted_by_with_type(): void
    {
        $reactable = new Article();
        $reactant = new NullReactant($reactable);
        $reacter = factory(Reacter::class)->make();
        $reactionType = new ReactionType();

        $isReacted = $reactant->isReactedByWithType($reacter, $reactionType);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_determine_is_not_reacted_by_with_type(): void
    {
        $reactable = new Article();
        $reactant = new NullReactant($reactable);
        $reacter = factory(Reacter::class)->make();
        $reactionType = new ReactionType();

        $isReacted = $reactant->isNotReactedByWithType($reacter, $reactionType);

        $this->assertTrue($isReacted);
    }
}
