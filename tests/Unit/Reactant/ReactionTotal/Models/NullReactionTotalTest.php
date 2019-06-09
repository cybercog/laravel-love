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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\ReactionTotal\Models;

use Cog\Contracts\Love\Reactant\ReactionTotal\Exceptions\ReactionTotalInvalid;
use Cog\Laravel\Love\Reactant\Models\NullReactant;
use Cog\Laravel\Love\Reactant\ReactionTotal\Models\NullReactionTotal;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\TestCase;

final class NullReactionTotalTest extends TestCase
{
    /** @test */
    public function it_can_get_count(): void
    {
        $reactant = new NullReactant(new Article());
        $total = new NullReactionTotal($reactant);

        $totalCount = $total->getCount();

        $this->assertSame(0, $totalCount);
    }

    /** @test */
    public function it_can_get_weight(): void
    {
        $reactant = new NullReactant(new Article());
        $total = new NullReactionTotal($reactant);

        $totalWeight = $total->getWeight();

        $this->assertSame(0, $totalWeight);
    }

    /** @test */
    public function it_can_get_reactant(): void
    {
        $reactable = new Article();
        $reactant = new NullReactant($reactable);
        $total = new NullReactionTotal($reactant);

        $assertReactant = $total->getReactant();

        $this->assertSame($reactant, $assertReactant);
    }

    /** @test */
    public function it_throws_exception_on_increment_count(): void
    {
        $this->expectException(ReactionTotalInvalid::class);

        $total = new NullReactionTotal(new NullReactant(new Article()));

        $total->incrementCount(2);
    }

    /** @test */
    public function it_throws_exception_on_decrement_count(): void
    {
        $this->expectException(ReactionTotalInvalid::class);

        $total = new NullReactionTotal(new NullReactant(new Article()));

        $total->decrementCount(2);
    }

    /** @test */
    public function it_throws_exception_on_increment_weight(): void
    {
        $this->expectException(ReactionTotalInvalid::class);

        $total = new NullReactionTotal(new NullReactant(new Article()));

        $total->incrementWeight(2);
    }

    /** @test */
    public function it_throws_exception_on_decrement_weight(): void
    {
        $this->expectException(ReactionTotalInvalid::class);

        $total = new NullReactionTotal(new NullReactant(new Article()));

        $total->decrementWeight(2);
    }
}
