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

namespace Cog\Tests\Laravel\Love\Unit\Reacter\Models;

use Cog\Contracts\Love\Reacter\Exceptions\ReacterInvalid;
use Cog\Laravel\Love\Reactant\Models\NullReactant;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reacter\Models\NullReacter;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\Bot;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Support\Collection;
use TypeError;

final class NullReacterTest extends TestCase
{
    /** @test */
    public function it_throws_exception_on_get_id_when_id_is_null(): void
    {
        $this->expectException(TypeError::class);

        $reacter = new NullReacter(new User());

        $reacter->getId();
    }

    /** @test */
    public function it_can_get_reacterable(): void
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);

        $assertReacterable = $reacter->getReacterable();

        $this->assertSame($reacterable, $assertReacterable);
    }

    /** @test */
    public function it_can_get_reactions(): void
    {
        $reacter = new NullReacter(new User());

        $reactions = $reacter->getReactions();

        $this->assertCount(0, $reactions);
        $this->assertIsIterable($reactions);
    }

    /** @test */
    public function it_can_get_reactions_collection(): void
    {
        $reacter = new NullReacter(new User());

        $reactions = $reacter->getReactions();

        $this->assertInstanceOf(Collection::class, $reactions);
    }

    /** @test */
    public function it_throws_reactant_invalid_on_react_to(): void
    {
        $this->expectException(ReacterInvalid::class);

        $reactionType = factory(ReactionType::class)->create();
        $reacter = new NullReacter(new Bot());
        $reactable = factory(Article::class)->create();
        $reactant = $reactable->loveReactant;

        $reacter->reactTo($reactant, $reactionType);
    }

    /** @test */
    public function it_throws_reactant_invalid_on_react_to_when_reactant_is_null_object(): void
    {
        $this->expectException(ReacterInvalid::class);

        $reactionType = factory(ReactionType::class)->create();
        $reacter = new NullReacter(new Bot());
        $reactant = new NullReactant(new Article());

        $reacter->reactTo($reactant, $reactionType);
    }

    /** @test */
    public function it_throws_reactant_invalid_on_unreact_to(): void
    {
        $this->expectException(ReacterInvalid::class);

        $reactionType = factory(ReactionType::class)->create();
        $reacter = new NullReacter(new Bot());
        $reactable = factory(Article::class)->create();
        $reactant = $reactable->loveReactant;

        $reacter->unreactTo($reactant, $reactionType);
    }

    /** @test */
    public function it_throws_reactant_invalid_on_unreact_to_when_reactant_is_null_object(): void
    {
        $this->expectException(ReacterInvalid::class);

        $reactionType = factory(ReactionType::class)->create();
        $reacter = new NullReacter(new Bot());
        $reactant = new NullReactant(new Article());

        $reacter->unreactTo($reactant, $reactionType);
    }

    /** @test */
    public function it_can_check_is_equal_to_self(): void
    {
        $nullReacter = new NullReacter(new User());

        $this->assertTrue($nullReacter->isEqualTo($nullReacter));
    }

    /** @test */
    public function it_can_check_is_equal_to_other_null_object_reacter(): void
    {
        $nullReacter = new NullReacter(new User());
        $otherNullReacter = new NullReacter(new User());

        $this->assertTrue($nullReacter->isEqualTo($otherNullReacter));
    }

    /** @test */
    public function it_can_check_is_equal_to_not_null_object_reacter(): void
    {
        $nullReacter = new NullReacter(new User());
        $reacter = factory(Reacter::class)->create();

        $this->assertFalse($nullReacter->isEqualTo($reacter));
    }

    /** @test */
    public function it_can_check_is_equal_to_not_null_object_reacter_and_not_persisted(): void
    {
        $nullReacter = new NullReacter(new User());
        $reacter = new Reacter();

        $this->assertFalse($nullReacter->isEqualTo($reacter));
    }

    /** @test */
    public function it_can_check_is_not_equal_to_self(): void
    {
        $nullReacter = new NullReacter(new User());

        $this->assertFalse($nullReacter->isNotEqualTo($nullReacter));
    }

    /** @test */
    public function it_can_check_is_not_equal_to_other_null_object_reacter(): void
    {
        $nullReacter = new NullReacter(new User());
        $otherNullReacter = new NullReacter(new User());

        $this->assertFalse($nullReacter->isNotEqualTo($otherNullReacter));
    }

    /** @test */
    public function it_can_check_is_not_equal_to_not_null_object_reacter(): void
    {
        $nullReacter = new NullReacter(new User());
        $reacter = factory(Reacter::class)->create();

        $this->assertTrue($nullReacter->isNotEqualTo($reacter));
    }

    /** @test */
    public function it_can_check_is_not_equal_to_not_null_object_reacter_and_not_persisted(): void
    {
        $nullReacter = new NullReacter(new User());
        $reacter = new Reacter();

        $this->assertTrue($nullReacter->isNotEqualTo($reacter));
    }

    /** @test */
    public function it_can_check_is_reacted_to(): void
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);
        $reactant = factory(Reactant::class)->make();

        $isReacted = $reacter->isReactedTo($reactant);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_to(): void
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);
        $reactant = factory(Reactant::class)->make();

        $isReacted = $reacter->isNotReactedTo($reactant);

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_is_reacted_to_with_type(): void
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);
        $reactant = factory(Reactant::class)->make();
        $reactionType = new ReactionType();

        $isReacted = $reacter->isReactedToWithType($reactant, $reactionType);

        $this->assertFalse($isReacted);
    }

    /** @test */
    public function it_can_check_is_not_reacted_to_with_type(): void
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);
        $reactant = factory(Reactant::class)->make();
        $reactionType = new ReactionType();

        $isReacted = $reacter->isNotReactedToWithType($reactant, $reactionType);

        $this->assertTrue($isReacted);
    }

    /** @test */
    public function it_can_check_is_null(): void
    {
        $reacter = new NullReacter(new User());

        $this->assertTrue($reacter->isNull());
    }

    /** @test */
    public function it_can_check_is_not_null(): void
    {
        $reacter = new NullReacter(new User());

        $this->assertFalse($reacter->isNotNull());
    }
}
