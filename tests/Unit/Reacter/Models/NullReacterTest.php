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
    public function test_throws_exception_on_get_id_when_id_is_null(): void
    {
        $this->expectException(TypeError::class);

        $reacter = new NullReacter(new User());

        $reacter->getId();
    }

    public function test_can_get_reacterable(): void
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);

        $assertReacterable = $reacter->getReacterable();

        $this->assertSame($reacterable, $assertReacterable);
    }

    public function test_can_get_reactions(): void
    {
        $reacter = new NullReacter(new User());

        $reactions = $reacter->getReactions();

        $this->assertCount(0, $reactions);
        $this->assertIsIterable($reactions);
    }

    public function test_can_get_reactions_collection(): void
    {
        $reacter = new NullReacter(new User());

        $reactions = $reacter->getReactions();

        $this->assertInstanceOf(Collection::class, $reactions);
    }

    public function test_throws_reactant_invalid_on_react_to(): void
    {
        $this->expectException(ReacterInvalid::class);

        $reactionType = ReactionType::factory()->create();
        $reacter = new NullReacter(new Bot());
        $reactable = Article::factory()->create();
        $reactant = $reactable->loveReactant;

        $reacter->reactTo($reactant, $reactionType);
    }

    public function test_throws_reactant_invalid_on_react_to_when_reactant_is_null_object(): void
    {
        $this->expectException(ReacterInvalid::class);

        $reactionType = ReactionType::factory()->create();
        $reacter = new NullReacter(new Bot());
        $reactant = new NullReactant(new Article());

        $reacter->reactTo($reactant, $reactionType);
    }

    public function test_throws_reactant_invalid_on_unreact_to(): void
    {
        $this->expectException(ReacterInvalid::class);

        $reactionType = ReactionType::factory()->create();
        $reacter = new NullReacter(new Bot());
        $reactable = Article::factory()->create();
        $reactant = $reactable->loveReactant;

        $reacter->unreactTo($reactant, $reactionType);
    }

    public function test_throws_reactant_invalid_on_unreact_to_when_reactant_is_null_object(): void
    {
        $this->expectException(ReacterInvalid::class);

        $reactionType = ReactionType::factory()->create();
        $reacter = new NullReacter(new Bot());
        $reactant = new NullReactant(new Article());

        $reacter->unreactTo($reactant, $reactionType);
    }

    public function test_can_check_is_equal_to_self(): void
    {
        $nullReacter = new NullReacter(new User());

        $this->assertTrue($nullReacter->isEqualTo($nullReacter));
    }

    public function test_can_check_is_equal_to_other_null_object_reacter(): void
    {
        $nullReacter = new NullReacter(new User());
        $otherNullReacter = new NullReacter(new User());

        $this->assertTrue($nullReacter->isEqualTo($otherNullReacter));
    }

    public function test_can_check_is_equal_to_not_null_object_reacter(): void
    {
        $nullReacter = new NullReacter(new User());
        $reacter = Reacter::factory()->create();

        $this->assertFalse($nullReacter->isEqualTo($reacter));
    }

    public function test_can_check_is_equal_to_not_null_object_reacter_and_not_persisted(): void
    {
        $nullReacter = new NullReacter(new User());
        $reacter = new Reacter();

        $this->assertFalse($nullReacter->isEqualTo($reacter));
    }

    public function test_can_check_is_not_equal_to_self(): void
    {
        $nullReacter = new NullReacter(new User());

        $this->assertFalse($nullReacter->isNotEqualTo($nullReacter));
    }

    public function test_can_check_is_not_equal_to_other_null_object_reacter(): void
    {
        $nullReacter = new NullReacter(new User());
        $otherNullReacter = new NullReacter(new User());

        $this->assertFalse($nullReacter->isNotEqualTo($otherNullReacter));
    }

    public function test_can_check_is_not_equal_to_not_null_object_reacter(): void
    {
        $nullReacter = new NullReacter(new User());
        $reacter = Reacter::factory()->create();

        $this->assertTrue($nullReacter->isNotEqualTo($reacter));
    }

    public function test_can_check_is_not_equal_to_not_null_object_reacter_and_not_persisted(): void
    {
        $nullReacter = new NullReacter(new User());
        $reacter = new Reacter();

        $this->assertTrue($nullReacter->isNotEqualTo($reacter));
    }

    public function test_can_check_has_reacted_to(): void
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);
        $reactant = Reactant::factory()->make();

        $isReacted = $reacter->hasReactedTo($reactant);

        $this->assertFalse($isReacted);
    }

    public function test_can_check_has_not_reacted_to(): void
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);
        $reactant = Reactant::factory()->make();

        $isReacted = $reacter->hasNotReactedTo($reactant);

        $this->assertTrue($isReacted);
    }

    public function test_can_check_has_reacted_to_with_type(): void
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);
        $reactant = Reactant::factory()->make();
        $reactionType = new ReactionType();

        $isReacted = $reacter->hasReactedTo($reactant, $reactionType);

        $this->assertFalse($isReacted);
    }

    public function test_can_check_has_not_reacted_to_with_type(): void
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);
        $reactant = Reactant::factory()->make();
        $reactionType = new ReactionType();

        $isReacted = $reacter->hasNotReactedTo($reactant, $reactionType);

        $this->assertTrue($isReacted);
    }

    public function test_can_check_has_reacted_to_with_rate(): void
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);
        $reactant = Reactant::factory()->make();

        $isReacted = $reacter->hasReactedTo($reactant, null, 2.0);

        $this->assertFalse($isReacted);
    }

    public function test_can_check_has_not_reacted_to_with_rate(): void
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);
        $reactant = Reactant::factory()->make();

        $isReacted = $reacter->hasNotReactedTo($reactant, null, 2.0);

        $this->assertTrue($isReacted);
    }

    public function test_can_check_has_reacted_to_with_type_and_rate(): void
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);
        $reactant = Reactant::factory()->make();
        $reactionType = new ReactionType();

        $isReacted = $reacter->hasReactedTo($reactant, $reactionType, 2.0);

        $this->assertFalse($isReacted);
    }

    public function test_can_check_has_not_reacted_to_with_type_and_rate(): void
    {
        $reacterable = new User();
        $reacter = new NullReacter($reacterable);
        $reactant = Reactant::factory()->make();
        $reactionType = new ReactionType();

        $isReacted = $reacter->hasNotReactedTo($reactant, $reactionType, 2.0);

        $this->assertTrue($isReacted);
    }

    public function test_can_check_is_null(): void
    {
        $reacter = new NullReacter(new User());

        $this->assertTrue($reacter->isNull());
    }

    public function test_can_check_is_not_null(): void
    {
        $reacter = new NullReacter(new User());

        $this->assertFalse($reacter->isNotNull());
    }
}
