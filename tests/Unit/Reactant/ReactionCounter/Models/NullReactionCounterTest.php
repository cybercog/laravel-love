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

use Cog\Contracts\Love\Reactant\ReactionCounter\Exceptions\ReactionCounterInvalid;
use Cog\Laravel\Love\Reactant\Models\NullReactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\NullReactionCounter;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\TestCase;

final class NullReactionCounterTest extends TestCase
{
    public function test_can_get_count(): void
    {
        $reactionType = ReactionType::factory()->create();
        $counter = new NullReactionCounter(new NullReactant(new Article()), $reactionType);

        $counterCount = $counter->getCount();

        $this->assertSame(0, $counterCount);
    }

    public function test_can_get_weight(): void
    {
        $reactionType = ReactionType::factory()->create();
        $counter = new NullReactionCounter(new NullReactant(new Article()), $reactionType);

        $counterWeight = $counter->getWeight();

        $this->assertSame(0.0, $counterWeight);
    }

    public function test_can_get_reactant(): void
    {
        $reactant = new NullReactant(new Article());
        $reactionType = ReactionType::factory()->create();
        $counter = new NullReactionCounter($reactant, $reactionType);

        $assertReactant = $counter->getReactant();

        $this->assertSame($reactant, $assertReactant);
    }

    public function test_can_get_reaction_type(): void
    {
        $reactant = new NullReactant(new Article());
        $reactionType = ReactionType::factory()->create();
        $counter = new NullReactionCounter($reactant, $reactionType);

        $assertReactionType = $counter->getReactionType();

        $this->assertSame($reactionType, $assertReactionType);
    }

    public function test_can_check_is_reaction_of_type(): void
    {
        $reactant = new NullReactant(new Article());
        $reactionType = ReactionType::factory()->create();
        $anotherReactionType = ReactionType::factory()->create();
        $counter = new NullReactionCounter($reactant, $reactionType);

        $true = $counter->isReactionOfType($reactionType);
        $false = $counter->isReactionOfType($anotherReactionType);

        $this->assertTrue($true);
        $this->assertFalse($false);
    }

    public function test_can_check_is_not_reaction_of_type(): void
    {
        $reactant = new NullReactant(new Article());
        $reactionType = ReactionType::factory()->create();
        $anotherReactionType = ReactionType::factory()->create();
        $counter = new NullReactionCounter($reactant, $reactionType);

        $true = $counter->isNotReactionOfType($anotherReactionType);
        $false = $counter->isNotReactionOfType($reactionType);

        $this->assertTrue($true);
        $this->assertFalse($false);
    }

    public function test_throws_exception_on_increment_count(): void
    {
        $this->expectException(ReactionCounterInvalid::class);

        $reactionType = ReactionType::factory()->create();
        $counter = new NullReactionCounter(new NullReactant(new Article()), $reactionType);

        $counter->incrementCount(2);
    }

    public function test_throws_exception_on_decrement_count(): void
    {
        $this->expectException(ReactionCounterInvalid::class);

        $reactionType = ReactionType::factory()->create();
        $counter = new NullReactionCounter(new NullReactant(new Article()), $reactionType);

        $counter->decrementCount(2);
    }

    public function test_throws_exception_on_increment_weight(): void
    {
        $this->expectException(ReactionCounterInvalid::class);

        $reactionType = ReactionType::factory()->create();
        $counter = new NullReactionCounter(new NullReactant(new Article()), $reactionType);

        $counter->incrementWeight(2);
    }

    public function test_throws_exception_on_decrement_weight(): void
    {
        $this->expectException(ReactionCounterInvalid::class);

        $reactionType = ReactionType::factory()->create();
        $counter = new NullReactionCounter(new NullReactant(new Article()), $reactionType);

        $counter->decrementWeight(2);
    }
}
