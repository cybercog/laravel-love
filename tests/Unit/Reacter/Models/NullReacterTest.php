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

namespace Cog\Tests\Laravel\Love\Unit\Reacter\Models;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reacter\Models\NullReacter;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class NullReacterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_get_reacterable()
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);

        $assertReacterable = $reacter->getReacterable();

        $this->assertSame($reacterable, $assertReacterable);
    }

    /** @test */
    public function it_can_get_reactions(): void
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);

        $reactions = $reacter->getReactions();

        $this->assertCount(0, $reactions);
        $this->assertInternalType('iterable', $reactions);
    }

    /** @test */
    public function it_can_determine_is_reacted_to(): void
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);
        $reactant = factory(Reactant::class)->make();

        $isReacted = $reacter->isReactedTo($reactant);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_determine_is_not_reacted_to(): void
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);
        $reactant = factory(Reactant::class)->make();

        $isReacted = $reacter->isNotReactedTo($reactant);

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_determine_is_reacted_with_type_to(): void
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);
        $reactant = factory(Reactant::class)->make();
        $reactionType = new ReactionType();

        $isReacted = $reacter->isReactedWithTypeTo($reactant, $reactionType);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_determine_is_not_reacted_with_type_to(): void
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);
        $reactant = factory(Reactant::class)->make();
        $reactionType = new ReactionType();

        $isReacted = $reacter->isNotReactedWithTypeTo($reactant, $reactionType);

        $this->assertTrue($isReacted);
    }
}
