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

namespace Cog\Tests\Laravel\Love\Unit\ReactionType\Models;

use Cog\Contracts\Love\ReactionType\Exceptions\ReactionTypeInvalid;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReactionTypeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_fill_name(): void
    {
        $type = new ReactionType([
            'name' => 'TestType',
        ]);

        $this->assertSame('TestType', $type->getAttribute('name'));
    }

    /** @test */
    public function it_can_fill_weight(): void
    {
        $type = new ReactionType([
            'weight' => 4,
        ]);

        $this->assertSame(4, $type->getAttribute('weight'));
    }

    /** @test */
    public function it_casts_weight_to_integer(): void
    {
        $type = new ReactionType([
            'weight' => '4',
        ]);

        $this->assertSame(4, $type->getAttribute('weight'));
    }

    /** @test */
    public function it_can_has_reaction(): void
    {
        $type = factory(ReactionType::class)->create();

        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $type->getKey(),
        ]);

        $assertReaction = $type->reactions->first();
        $this->assertTrue($assertReaction->is($reaction));
    }

    /** @test */
    public function it_can_has_many_reactions(): void
    {
        $type = factory(ReactionType::class)->create();

        $reactions = factory(Reaction::class, 2)->create([
            'reaction_type_id' => $type->getKey(),
        ]);

        $assertReactions = $type->reactions;
        $this->assertTrue($assertReactions->get(0)->is($reactions->get(0)));
        $this->assertTrue($assertReactions->get(1)->is($reactions->get(1)));
    }

    /** @test */
    public function it_can_instantiate_reaction_type_from_name(): void
    {
        $type = factory(ReactionType::class)->create([
            'name' => 'TestType',
        ]);

        $assertType = ReactionType::fromName('TestType');

        $this->assertTrue($assertType->is($type));
    }

    /** @test */
    public function it_can_get_name(): void
    {
        $type = new ReactionType([
            'name' => 'TestType',
        ]);

        $this->assertSame('TestType', $type->getName());
    }

    /** @test */
    public function it_can_get_name_if_not_set(): void
    {
        $type = new ReactionType();

        $this->assertSame('', $type->getName());
    }

    /** @test */
    public function it_can_get_weight(): void
    {
        $type = new ReactionType([
            'weight' => '4',
        ]);

        $this->assertSame(4, $type->getWeight());
    }

    /** @test */
    public function it_can_get_weight_if_not_set(): void
    {
        $type = new ReactionType();

        $this->assertSame(0, $type->getWeight());
    }

    /** @test */
    public function it_can_determine_is_equal_to(): void
    {
        $type = factory(ReactionType::class)->create();
        $otherType = factory(ReactionType::class)->create();

        $this->assertTrue($type->isEqualTo($type));
        $this->assertFalse($type->isEqualTo($otherType));
    }

    /** @test */
    public function it_can_determine_is_not_equal_to(): void
    {
        $type = factory(ReactionType::class)->create();
        $otherType = factory(ReactionType::class)->create();

        $this->assertTrue($type->isNotEqualTo($otherType));
        $this->assertFalse($type->isNotEqualTo($type));
    }

    /** @test */
    public function it_throws_exception_if_instantiate_reaction_type_from_not_exist_name(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        factory(ReactionType::class)->create();

        ReactionType::fromName('NotExistType');
    }

    /** @test */
    public function it_should_not_perform_database_queries_on_instantiation_from_name(): void
    {
        factory(ReactionType::class)->create([
            'name' => 'LikeType',
        ]);
        factory(ReactionType::class)->create([
            'name' => 'DislikeType',
        ]);
        $db = $this->app->make(ConnectionInterface::class);
        $db->enableQueryLog();

        $like1 = ReactionType::fromName('LikeType');
        $dislike1 = ReactionType::fromName('DislikeType');
        $dislike2 = ReactionType::fromName('DislikeType');
        $like2 = ReactionType::fromName('LikeType');
        $dislike3 = ReactionType::fromName('DislikeType');

        $queries = $db->getQueryLog();
        $this->assertCount(0, $queries);
        $this->assertSame('LikeType', $like1->getName());
        $this->assertSame('LikeType', $like2->getName());
        $this->assertSame('DislikeType', $dislike1->getName());
        $this->assertSame('DislikeType', $dislike2->getName());
        $this->assertSame('DislikeType', $dislike3->getName());
    }

    /** @test */
    public function it_updates_type_in_registry_if_model_was_changed(): void
    {
        $type = factory(ReactionType::class)->create([
            'name' => 'TestRegistryUpdate',
            'weight' => 4,
        ]);
        $type->update([
            'weight' => 8,
        ]);

        $typeFromRegistry = ReactionType::fromName('TestRegistryUpdate');

        $this->assertSame(8, $type->getWeight());
        $this->assertSame(8, $typeFromRegistry->getWeight());
    }

    /** @test */
    public function it_deletes_type_from_registry_if_model_was_deleted(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        $type = factory(ReactionType::class)->create([
            'name' => 'TestRegistryDelete',
        ]);
        $type->delete();

        ReactionType::fromName('TestRegistryDelete');
    }
}
