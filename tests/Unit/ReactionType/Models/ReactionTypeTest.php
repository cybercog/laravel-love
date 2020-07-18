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

namespace Cog\Tests\Laravel\Love\Unit\ReactionType\Models;

use Cog\Contracts\Love\ReactionType\Exceptions\ReactionTypeInvalid;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Database\ConnectionInterface;
use TypeError;

final class ReactionTypeTest extends TestCase
{
    /** @test */
    public function it_can_fill_name(): void
    {
        $type = new ReactionType([
            'name' => 'TestType',
        ]);

        $this->assertSame('TestType', $type->getAttribute('name'));
    }

    /** @test */
    public function it_can_fill_mass(): void
    {
        $type = new ReactionType([
            'mass' => 4,
        ]);

        $this->assertSame(4, $type->getAttribute('mass'));
    }

    /** @test */
    public function it_has_default_rate_value(): void
    {
        $type = new ReactionType();

        $this->assertSame(ReactionType::MASS_DEFAULT, $type->getAttribute('mass'));
        $this->assertSame(ReactionType::MASS_DEFAULT, $type->getMass());
    }

    /** @test */
    public function it_casts_id_to_string(): void
    {
        $type = factory(ReactionType::class)->make([
            'id' => 4,
        ]);

        $this->assertSame('4', $type->getAttribute('id'));
        $this->assertSame('4', $type->getId());
    }

    /** @test */
    public function it_casts_mass_to_integer(): void
    {
        $type = new ReactionType([
            'mass' => '4',
        ]);

        $this->assertSame(4, $type->getAttribute('mass'));
        $this->assertSame(4, $type->getMass());
    }

    /** @test */
    public function it_can_has_reaction(): void
    {
        $type = factory(ReactionType::class)->create();

        $reaction = factory(Reaction::class)->create([
            'reaction_type_id' => $type->getId(),
        ]);

        $assertReaction = $type->reactions->first();
        $this->assertTrue($assertReaction->is($reaction));
    }

    /** @test */
    public function it_can_has_many_reactions(): void
    {
        $type = factory(ReactionType::class)->create();

        $reactions = factory(Reaction::class, 2)->create([
            'reaction_type_id' => $type->getId(),
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

        $this->assertTrue($type->is($assertType));
    }

    /** @test */
    public function it_can_get_id(): void
    {
        $type = factory(ReactionType::class)->make([
            'id' => '4',
        ]);

        $this->assertSame('4', $type->getId());
    }

    /** @test */
    public function it_throws_exception_on_get_id_when_id_is_null(): void
    {
        $this->expectException(TypeError::class);

        $type = new ReactionType();

        $type->getId();
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
    public function it_throws_exception_on_get_name_when_name_is_null(): void
    {
        $this->expectException(TypeError::class);

        $type = new ReactionType();

        $type->getName();
    }

    /** @test */
    public function it_can_get_mass(): void
    {
        $type = new ReactionType([
            'mass' => '4',
        ]);

        $this->assertSame(4, $type->getMass());
    }

    /** @test */
    public function it_can_get_mass_when_mass_is_null(): void
    {
        $type = new ReactionType();

        $this->assertSame(0, $type->getMass());
    }

    /** @test */
    public function it_can_check_is_equal_to(): void
    {
        $type = factory(ReactionType::class)->create();
        $otherType = factory(ReactionType::class)->create();

        $this->assertTrue($type->isEqualTo($type));
        $this->assertFalse($type->isEqualTo($otherType));
    }

    /** @test */
    public function it_can_check_is_not_equal_to(): void
    {
        $type = factory(ReactionType::class)->create();
        $otherType = factory(ReactionType::class)->create();

        $this->assertFalse($type->isNotEqualTo($type));
        $this->assertTrue($type->isNotEqualTo($otherType));
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

        $this->assertCount(0, $db->getQueryLog());
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
            'mass' => 4,
        ]);
        $type->update([
            'mass' => 8,
        ]);
        $db = $this->app->make(ConnectionInterface::class);
        $db->enableQueryLog();

        $typeFromRegistry = ReactionType::fromName('TestRegistryUpdate');

        $this->assertCount(0, $db->getQueryLog());
        $this->assertSame(8, $type->getMass());
        $this->assertSame(8, $typeFromRegistry->getMass());
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
