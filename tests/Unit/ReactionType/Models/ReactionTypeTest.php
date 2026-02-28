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
    public function test_can_fill_name(): void
    {
        $type = new ReactionType([
            'name' => 'TestType',
        ]);

        $this->assertSame('TestType', $type->getAttribute('name'));
    }

    public function test_can_fill_mass(): void
    {
        $type = new ReactionType([
            'mass' => 4,
        ]);

        $this->assertSame(4, $type->getAttribute('mass'));
    }

    public function test_has_default_rate_value(): void
    {
        $type = new ReactionType();

        $this->assertSame(ReactionType::MASS_DEFAULT, $type->getAttribute('mass'));
        $this->assertSame(ReactionType::MASS_DEFAULT, $type->getMass());
    }

    public function test_casts_id_to_string(): void
    {
        $type = ReactionType::factory()->make([
            'id' => 4,
        ]);

        $this->assertSame('4', $type->getAttribute('id'));
        $this->assertSame('4', $type->getId());
    }

    public function test_casts_mass_to_integer(): void
    {
        $type = new ReactionType([
            'mass' => '4',
        ]);

        $this->assertSame(4, $type->getAttribute('mass'));
        $this->assertSame(4, $type->getMass());
    }

    public function test_can_has_reaction(): void
    {
        $type = ReactionType::factory()->create();

        $reaction = Reaction::factory()->create([
            'reaction_type_id' => $type->getId(),
        ]);

        $assertReaction = $type->reactions->first();
        $this->assertTrue($assertReaction->is($reaction));
    }

    public function test_can_has_many_reactions(): void
    {
        $type = ReactionType::factory()->create();

        $reactions = Reaction::factory()->count(2)->create([
            'reaction_type_id' => $type->getId(),
        ]);

        $assertReactions = $type->reactions;
        $this->assertTrue($assertReactions->get(0)->is($reactions->get(0)));
        $this->assertTrue($assertReactions->get(1)->is($reactions->get(1)));
    }

    public function test_can_instantiate_reaction_type_from_name(): void
    {
        $type = ReactionType::factory()->create([
            'name' => 'TestType',
        ]);

        $assertType = ReactionType::fromName('TestType');

        $this->assertTrue($type->is($assertType));
    }

    public function test_can_get_id(): void
    {
        $type = ReactionType::factory()->make([
            'id' => '4',
        ]);

        $this->assertSame('4', $type->getId());
    }

    public function test_throws_exception_on_get_id_when_id_is_null(): void
    {
        $this->expectException(TypeError::class);

        $type = new ReactionType();

        $type->getId();
    }

    public function test_can_get_name(): void
    {
        $type = new ReactionType([
            'name' => 'TestType',
        ]);

        $this->assertSame('TestType', $type->getName());
    }

    public function test_throws_exception_on_get_name_when_name_is_null(): void
    {
        $this->expectException(TypeError::class);

        $type = new ReactionType();

        $type->getName();
    }

    public function test_can_get_mass(): void
    {
        $type = new ReactionType([
            'mass' => '4',
        ]);

        $this->assertSame(4, $type->getMass());
    }

    public function test_can_get_mass_when_mass_is_null(): void
    {
        $type = new ReactionType();

        $this->assertSame(0, $type->getMass());
    }

    public function test_can_check_is_equal_to(): void
    {
        $type = ReactionType::factory()->create();
        $otherType = ReactionType::factory()->create();

        $this->assertTrue($type->isEqualTo($type));
        $this->assertFalse($type->isEqualTo($otherType));
    }

    public function test_can_check_is_not_equal_to(): void
    {
        $type = ReactionType::factory()->create();
        $otherType = ReactionType::factory()->create();

        $this->assertFalse($type->isNotEqualTo($type));
        $this->assertTrue($type->isNotEqualTo($otherType));
    }

    public function test_throws_exception_if_instantiate_reaction_type_from_not_exist_name(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        ReactionType::factory()->create();

        ReactionType::fromName('NotExistType');
    }

    public function test_should_not_perform_database_queries_on_instantiation_from_name(): void
    {
        ReactionType::factory()->create([
            'name' => 'LikeType',
        ]);
        ReactionType::factory()->create([
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

    public function test_updates_type_in_registry_if_model_was_changed(): void
    {
        $type = ReactionType::factory()->create([
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

    public function test_deletes_type_from_registry_if_model_was_deleted(): void
    {
        $this->expectException(ReactionTypeInvalid::class);

        $type = ReactionType::factory()->create([
            'name' => 'TestRegistryDelete',
        ]);
        $type->delete();

        ReactionType::fromName('TestRegistryDelete');
    }
}
