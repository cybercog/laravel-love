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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\ReactionCounter\Models;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class ReactionCounterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_fill_count(): void
    {
        $counter = new ReactionCounter([
            'count' => 4,
        ]);

        $this->assertSame(4, $counter->getAttribute('count'));
    }

    /** @test */
    public function it_can_fill_weight(): void
    {
        $counter = new ReactionCounter([
            'weight' => 4,
        ]);

        $this->assertSame(4, $counter->getAttribute('weight'));
    }

    /** @test */
    public function it_can_fill_reaction_type_id(): void
    {
        $counter = new ReactionCounter([
            'reaction_type_id' => 4,
        ]);

        $this->assertSame(4, $counter->getAttribute('reaction_type_id'));
    }

    /** @test */
    public function it_casts_count_to_integer(): void
    {
        $counter = new ReactionCounter([
            'count' => '4',
        ]);

        $this->assertSame(4, $counter->getAttribute('count'));
    }

    /** @test */
    public function it_casts_weight_to_integer(): void
    {
        $counter = new ReactionCounter([
            'weight' => '4',
        ]);

        $this->assertSame(4, $counter->getAttribute('weight'));
    }

    /** @test */
    public function it_can_belong_to_reactant(): void
    {
        $reactant = factory(Reactant::class)->create();

        $counter = factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($counter->reactant->is($reactant));
    }

    /** @test */
    public function it_can_belong_to_reaction_type(): void
    {
        $reactionType = factory(ReactionType::class)->create();

        $counter = factory(ReactionCounter::class)->create([
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $this->assertTrue($counter->reactionType->is($reactionType));
    }

    /** @test */
    public function it_can_get_reactant(): void
    {
        $reactant = factory(Reactant::class)->create();

        $counter = factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($counter->getReactant()->is($reactant));
    }

    /** @test */
    public function it_can_get_reaction_type(): void
    {
        $reactionType = factory(ReactionType::class)->create();

        $counter = factory(ReactionCounter::class)->create([
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $this->assertTrue($counter->getReactionType()->is($reactionType));
    }

    /** @test */
    public function it_can_determine_is_reaction_of_type(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $anotherReactionType = factory(ReactionType::class)->create();

        $counter = factory(ReactionCounter::class)->create([
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $this->assertTrue($counter->isReactionOfType($reactionType));
        $this->assertFalse($counter->isReactionOfType($anotherReactionType));
    }

    /** @test */
    public function it_can_determine_is_not_reaction_of_type(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $anotherReactionType = factory(ReactionType::class)->create();

        $counter = factory(ReactionCounter::class)->create([
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $this->assertTrue($counter->isNotReactionOfType($anotherReactionType));
        $this->assertFalse($counter->isNotReactionOfType($reactionType));
    }

    /** @test */
    public function it_can_get_count(): void
    {
        $counter = new ReactionCounter([
            'count' => '4',
        ]);

        $this->assertSame(4, $counter->getCount());
    }

    /** @test */
    public function it_can_get_count_if_not_set(): void
    {
        $counter = new ReactionCounter();

        $this->assertSame(0, $counter->getCount());
    }

    /** @test */
    public function it_can_get_weight(): void
    {
        $counter = new ReactionCounter([
            'weight' => '4',
        ]);

        $this->assertSame(4, $counter->getWeight());
    }

    /** @test */
    public function it_can_get_weight_if_not_set(): void
    {
        $counter = new ReactionCounter();

        $this->assertSame(0, $counter->getWeight());
    }
}
