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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\ReactionCounter\Models;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use TypeError;

final class ReactionCounterTest extends TestCase
{
    /** @test */
    public function it_can_fill_count(): void
    {
        $counter = new ReactionCounter([
            'count' => 4,
        ]);

        $this->assertSame(4, $counter->getCount());
    }

    /** @test */
    public function it_can_fill_weight(): void
    {
        $counter = new ReactionCounter([
            'weight' => 4.1,
        ]);

        $this->assertSame(4.1, $counter->getWeight());
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
    public function it_casts_float_count_to_int(): void
    {
        $counter = new ReactionCounter([
            'count' => 4.0,
        ]);

        $this->assertSame(4, $counter->getCount());
    }

    /** @test */
    public function it_casts_string_count_to_int(): void
    {
        $counter = new ReactionCounter([
            'count' => '4',
        ]);

        $this->assertSame(4, $counter->getCount());
    }

    /** @test */
    public function it_casts_null_count_to_zero(): void
    {
        $counter = ReactionCounter::factory()->create([
            'count' => null,
        ]);

        $this->assertSame(0, $counter->getCount());
    }

    /** @test */
    public function it_casts_not_set_count_to_zero(): void
    {
        $counter = new ReactionCounter();

        $this->assertSame(0, $counter->getCount());
    }

    /** @test */
    public function it_casts_int_weight_to_float(): void
    {
        $counter = new ReactionCounter([
            'weight' => 4,
        ]);

        $this->assertSame(4.0, $counter->getWeight());
    }

    /** @test */
    public function it_casts_string_weight_to_float(): void
    {
        $counter = new ReactionCounter([
            'weight' => '4',
        ]);

        $this->assertSame(4.0, $counter->getWeight());
    }

    /** @test */
    public function it_casts_null_weight_to_zero(): void
    {
        $counter = ReactionCounter::factory()->create([
            'weight' => null,
        ]);

        $this->assertSame(0.0, $counter->getWeight());
    }

    /** @test */
    public function it_casts_not_set_weight_to_zero(): void
    {
        $counter = new ReactionCounter();

        $this->assertSame(0.0, $counter->getWeight());
    }

    /** @test */
    public function it_can_belong_to_reactant(): void
    {
        $reactant = Reactant::factory()->create();

        $counter = ReactionCounter::factory()->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($counter->reactant->is($reactant));
    }

    /** @test */
    public function it_can_belong_to_reaction_type(): void
    {
        $reactionType = ReactionType::factory()->create();

        $counter = ReactionCounter::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $this->assertTrue($counter->reactionType->is($reactionType));
    }

    /** @test */
    public function it_can_get_reactant(): void
    {
        $reactant = Reactant::factory()->create();

        $counter = ReactionCounter::factory()->create([
            'reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($counter->getReactant()->is($reactant));
    }

    /** @test */
    public function it_throws_exception_on_get_reactant_when_reactant_is_null(): void
    {
        $this->expectException(TypeError::class);

        $counter = new ReactionCounter();

        $counter->getReactant();
    }

    /** @test */
    public function it_can_get_reaction_type(): void
    {
        $reactionType = ReactionType::factory()->create();

        $counter = ReactionCounter::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $this->assertTrue($counter->getReactionType()->is($reactionType));
    }

    /** @test */
    public function it_throws_exception_on_get_reaction_type_when_reaction_type_is_null(): void
    {
        $this->expectException(TypeError::class);

        $counter = new ReactionCounter();

        $counter->getReactionType();
    }

    /** @test */
    public function it_can_check_is_reaction_of_type(): void
    {
        $reactionType = ReactionType::factory()->create();
        $anotherReactionType = ReactionType::factory()->create();
        $counter = ReactionCounter::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $true = $counter->isReactionOfType($reactionType);
        $false = $counter->isReactionOfType($anotherReactionType);

        $this->assertTrue($true);
        $this->assertFalse($false);
    }

    /** @test */
    public function it_can_check_is_not_reaction_of_type(): void
    {
        $reactionType = ReactionType::factory()->create();
        $anotherReactionType = ReactionType::factory()->create();
        $counter = ReactionCounter::factory()->create([
            'reaction_type_id' => $reactionType->getId(),
        ]);

        $true = $counter->isNotReactionOfType($anotherReactionType);
        $false = $counter->isNotReactionOfType($reactionType);

        $this->assertTrue($true);
        $this->assertFalse($false);
    }

    /** @test */
    public function it_can_increment_count(): void
    {
        $counter = ReactionCounter::factory()->create([
            'count' => 0,
        ]);

        $counter->incrementCount(2);

        $this->assertSame(2, $counter->getCount());
    }

    /** @test */
    public function it_can_increment_count_many_times(): void
    {
        $counter = ReactionCounter::factory()->create([
            'count' => 0,
        ]);

        $counter->incrementCount(2);
        $counter->incrementCount(3);

        $this->assertSame(5, $counter->getCount());
    }

    /** @test */
    public function it_can_decrement_count(): void
    {
        $counter = ReactionCounter::factory()->create([
            'count' => 10,
        ]);

        $counter->decrementCount(2);

        $this->assertSame(8, $counter->getCount());
    }

    /** @test */
    public function it_can_decrement_count_many_times(): void
    {
        $counter = ReactionCounter::factory()->create([
            'count' => 10,
        ]);

        $counter->decrementCount(2);
        $counter->decrementCount(3);

        $this->assertSame(5, $counter->getCount());
    }

    /** @test */
    public function it_can_increment_weight(): void
    {
        $counter = ReactionCounter::factory()->create([
            'weight' => 0,
        ]);

        $counter->incrementWeight(2);

        $this->assertSame(2.0, $counter->getWeight());
    }

    /** @test */
    public function it_can_increment_weight_many_times(): void
    {
        $counter = ReactionCounter::factory()->create([
            'weight' => 0,
        ]);

        $counter->incrementWeight(2);
        $counter->incrementWeight(3);

        $this->assertSame(5.0, $counter->getWeight());
    }

    /** @test */
    public function it_can_decrement_weight(): void
    {
        $counter = ReactionCounter::factory()->create([
            'weight' => 10,
        ]);

        $counter->decrementWeight(2);

        $this->assertSame(8.0, $counter->getWeight());
    }

    /** @test */
    public function it_can_decrement_weight_many_times(): void
    {
        $counter = ReactionCounter::factory()->create([
            'weight' => 10,
        ]);

        $counter->decrementWeight(2);
        $counter->decrementWeight(3);

        $this->assertSame(5.0, $counter->getWeight());
    }
}
