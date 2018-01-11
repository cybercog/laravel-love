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

namespace Cog\Tests\Laravel\Love\Unit\Likeable\Models\Traits;

use Cog\Contracts\Love\Like\Models\Like as LikeContract;
use Cog\Tests\Laravel\Love\Stubs\Models\Entity;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

/**
 * Class LikeableTest.
 *
 * @package Cog\Tests\Laravel\Love\Unit\Likeable\Models\Traits
 */
class LikeableTest extends TestCase
{
    use DatabaseTransactions;

    /* Likes */

    /** @test */
    public function it_can_like_by_current_user()
    {
        $entity = factory(Entity::class)->create();

        $user = factory(User::class)->create();
        $this->actingAs($user);

        $entity->likeBy();

        $this->assertEquals(1, $entity->likesCount);
        $this->assertEquals($user->id, $entity->likes->first()->user_id);
    }

    /** @test */
    public function it_can_like_by_concrete_user()
    {
        $entity = factory(Entity::class)->create();

        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $this->actingAs($user1);

        $entity->likeBy($user2->id);

        $this->assertEquals(1, $entity->likesCount);
        $this->assertEquals($user2->id, $entity->likes->first()->user_id);
    }

    /** @test */
    public function it_can_has_multiple_likes()
    {
        $entity = factory(Entity::class)->create();

        $entity->likeBy(1);
        $entity->likeBy(2);
        $entity->likeBy(3);
        $entity->likeBy(4);

        $this->assertEquals(4, $entity->likesCount);
    }

    /** @test */
    public function it_cannot_duplicate_likes()
    {
        $entity = factory(Entity::class)->create();

        $entity->likeBy(1);
        $entity->likeBy(1);

        $this->assertEquals(1, $entity->likesCount);
    }

    /** @test */
    public function it_can_unlikeBy()
    {
        $entity = factory(Entity::class)->create();
        $entity->likeBy(1);

        $entity->unlikeBy(1);

        $this->assertEquals(0, $entity->likesCount);
    }

    /** @test */
    public function it_cannot_unlike_by_user_if_not_liked()
    {
        $entity = factory(Entity::class)->create();
        $entity->likeBy(1);

        $entity->unlikeBy(2);

        $this->assertEquals(1, $entity->likesCount);
    }

    /** @test */
    public function it_can_add_like_with_toggle_by_current_user()
    {
        $entity = factory(Entity::class)->create();

        $user = factory(User::class)->create();
        $this->actingAs($user);

        $entity->toggleLikeBy();

        $this->assertEquals(1, $entity->likesCount);
        $this->assertEquals($user->id, $entity->likes->first()->user_id);
    }

    /** @test */
    public function it_can_remove_like_with_toggle_by_current_user()
    {
        $entity = factory(Entity::class)->create();

        $user = factory(User::class)->create();
        $this->actingAs($user);
        $entity->likeBy();

        $entity->toggleLikeBy();

        $this->assertEquals(0, $entity->likesCount);
    }

    /** @test */
    public function it_can_add_like_with_toggle_by_concrete_user()
    {
        $entity = factory(Entity::class)->create();

        $entity->toggleLikeBy(1);
        $this->assertEquals(1, $entity->likesCount);
    }

    /** @test */
    public function it_can_remove_like_with_toggle_by_concrete_user()
    {
        $entity = factory(Entity::class)->create();
        $entity->likeBy(1);

        $entity->toggleLikeBy(1);
        $this->assertEquals(0, $entity->likesCount);
    }

    /** @test */
    public function it_can_check_if_entity_liked_by_current_user()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $entity = factory(Entity::class)->create();
        $entity->likeBy();

        $this->assertTrue($entity->isLikedBy());
    }

    /** @test */
    public function it_can_check_if_entity_liked_by_concrete_user()
    {
        $entity = factory(Entity::class)->create();
        $entity->likeBy(1);

        $this->assertTrue($entity->isLikedBy(1));
        $this->assertFalse($entity->isLikedBy(2));
    }

    /** @test */
    public function it_can_check_if_entity_liked_by_current_user_using_attribute()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $entity = factory(Entity::class)->create();
        $entity->likeBy();

        $this->assertTrue($entity->liked);
    }

    /** @test */
    public function it_can_get_where_liked_by_current_user()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        factory(Entity::class)->create()->likeBy($user->id);
        factory(Entity::class)->create()->likeBy($user->id);
        factory(Entity::class)->create()->likeBy($user->id);

        $likedEntities = Entity::whereLikedBy()->get();

        $this->assertCount(3, $likedEntities);
    }

    /** @test */
    public function it_can_get_where_liked_by_concrete_user()
    {
        factory(Entity::class)->create()->likeBy(1);
        factory(Entity::class)->create()->likeBy(1);
        factory(Entity::class)->create()->likeBy(1);

        $likedEntities = Entity::whereLikedBy(1)->get();
        $shouldBeEmpty = Entity::whereLikedBy(2)->get();

        $this->assertCount(3, $likedEntities);
        $this->assertEmpty($shouldBeEmpty);
    }

    /* Dislikes */

    /** @test */
    public function it_can_dislike_by_current_user()
    {
        $entity = factory(Entity::class)->create();

        $user = factory(User::class)->create();
        $this->actingAs($user);

        $entity->dislikeBy();

        $this->assertEquals(1, $entity->dislikesCount);
        $this->assertEquals($user->id, $entity->dislikes->first()->user_id);
    }

    /** @test */
    public function it_can_dislike_by_concrete_user()
    {
        $entity = factory(Entity::class)->create();

        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $this->actingAs($user1);

        $entity->dislikeBy($user2->id);

        $this->assertEquals(1, $entity->dislikesCount);
        $this->assertEquals($user2->id, $entity->dislikes->first()->user_id);
    }

    /** @test */
    public function it_can_has_multiple_dislikes()
    {
        $entity = factory(Entity::class)->create();

        $entity->dislikeBy(1);
        $entity->dislikeBy(2);
        $entity->dislikeBy(3);
        $entity->dislikeBy(4);

        $this->assertEquals(4, $entity->dislikesCount);
    }

    /** @test */
    public function it_cannot_duplicate_dislikes()
    {
        $entity = factory(Entity::class)->create();

        $entity->dislikeBy(1);
        $entity->dislikeBy(1);

        $this->assertEquals(1, $entity->dislikesCount);
    }

    /** @test */
    public function it_can_undislikeBy()
    {
        $entity = factory(Entity::class)->create();
        $entity->dislikeBy(1);

        $entity->undislikeBy(1);

        $this->assertEquals(0, $entity->dislikesCount);
    }

    /** @test */
    public function it_cannot_undislike_by_user_if_not_disliked()
    {
        $entity = factory(Entity::class)->create();
        $entity->dislikeBy(1);

        $entity->undislikeBy(2);

        $this->assertEquals(1, $entity->dislikesCount);
    }

    /** @test */
    public function it_can_add_dislike_with_toggle_by_current_user()
    {
        $entity = factory(Entity::class)->create();

        $user = factory(User::class)->create();
        $this->actingAs($user);

        $entity->toggleDislikeBy();

        $this->assertEquals(1, $entity->dislikesCount);
        $this->assertEquals($user->id, $entity->dislikes->first()->user_id);
    }

    /** @test */
    public function it_can_remove_dislike_with_toggle_by_current_user()
    {
        $entity = factory(Entity::class)->create();

        $user = factory(User::class)->create();
        $this->actingAs($user);
        $entity->dislikeBy();

        $entity->toggleDislikeBy();

        $this->assertEquals(0, $entity->dislikesCount);
    }

    /** @test */
    public function it_can_add_dislike_with_toggle_by_concrete_user()
    {
        $entity = factory(Entity::class)->create();

        $entity->toggleDislikeBy(1);
        $this->assertEquals(1, $entity->dislikesCount);
    }

    /** @test */
    public function it_can_remove_dislike_with_toggle_by_concrete_user()
    {
        $entity = factory(Entity::class)->create();
        $entity->dislikeBy(1);

        $entity->toggleDislikeBy(1);
        $this->assertEquals(0, $entity->dislikesCount);
    }

    /** @test */
    public function it_can_check_if_entity_disliked_by_current_user()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $entity = factory(Entity::class)->create();
        $entity->dislikeBy();

        $this->assertTrue($entity->isDislikedBy());
    }

    /** @test */
    public function it_can_check_if_entity_disliked_by_concrete_user()
    {
        $entity = factory(Entity::class)->create();
        $entity->dislikeBy(1);

        $this->assertTrue($entity->isDislikedBy(1));
        $this->assertFalse($entity->isDislikedBy(2));
    }

    /** @test */
    public function it_can_check_if_entity_disliked_by_current_user_using_attribute()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $entity = factory(Entity::class)->create();
        $entity->dislikeBy();

        $this->assertTrue($entity->disliked);
    }

    /** @test */
    public function it_can_get_where_disliked_by_current_user()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        factory(Entity::class)->create()->dislikeBy($user->id);
        factory(Entity::class)->create()->dislikeBy($user->id);
        factory(Entity::class)->create()->dislikeBy($user->id);

        $dislikedEntities = Entity::whereDislikedBy()->get();

        $this->assertCount(3, $dislikedEntities);
    }

    /** @test */
    public function it_can_get_where_disliked_by_concrete_user()
    {
        factory(Entity::class)->create()->dislikeBy(1);
        factory(Entity::class)->create()->dislikeBy(1);
        factory(Entity::class)->create()->dislikeBy(1);

        $dislikedEntities = Entity::whereDislikedBy(1)->get();
        $shouldBeEmpty = Entity::whereDislikedBy(2)->get();

        $this->assertCount(3, $dislikedEntities);
        $this->assertEmpty($shouldBeEmpty);
    }

    /* Likes & Dislikes */

    /** @test */
    public function it_can_get_likes_relation()
    {
        $entity = factory(Entity::class)->create();

        $entity->likeBy(1);

        $this->assertInstanceOf(LikeContract::class, $entity->likes->first());
        $this->assertCount(1, $entity->likes);
    }

    /** @test */
    public function it_can_get_dislikes_relation()
    {
        $entity = factory(Entity::class)->create();

        $entity->dislikeBy(1);

        $this->assertInstanceOf(LikeContract::class, $entity->dislikes->first());
        $this->assertCount(1, $entity->dislikes);
    }

    /** @test */
    public function it_uses_eager_loaded_likes_relation_on_liked_check()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $entity1 = factory(Entity::class)->create();
        $entity2 = factory(Entity::class)->create();
        $entity3 = factory(Entity::class)->create();
        $entity1->likeBy($user->id);
        $entity3->likeBy($user->id);

        DB::enableQueryLog();
        $entities = Entity::with('likes')->get();
        $entitiesIsLiked = [];
        foreach ($entities as $entity) {
            $entitiesIsLiked[] = $entity->isLikedBy();
        }
        $queryLog = DB::getQueryLog();

        $this->assertCount(2, $queryLog);
        $this->assertTrue($entitiesIsLiked[0]);
        $this->assertFalse($entitiesIsLiked[1]);
        $this->assertTrue($entitiesIsLiked[2]);
    }

    /** @test */
    public function it_uses_eager_loaded_likes_and_dislikes_relation_on_liked_check()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $entity1 = factory(Entity::class)->create();
        $entity2 = factory(Entity::class)->create();
        $entity3 = factory(Entity::class)->create();
        $entity1->likeBy($user->id);
        $entity3->likeBy($user->id);

        DB::enableQueryLog();
        $entities = Entity::with('likesAndDislikes')->get();
        $entitiesIsLiked = [];
        foreach ($entities as $entity) {
            $entitiesIsLiked[] = $entity->isLikedBy();
        }
        $queryLog = DB::getQueryLog();

        $this->assertCount(2, $queryLog);
        $this->assertTrue($entitiesIsLiked[0]);
        $this->assertFalse($entitiesIsLiked[1]);
        $this->assertTrue($entitiesIsLiked[2]);
    }

    /** @test */
    public function it_does_not_use_eager_loaded_dislikes_relation_on_liked_check()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $entity1 = factory(Entity::class)->create();
        $entity2 = factory(Entity::class)->create();
        $entity3 = factory(Entity::class)->create();
        $entity1->likeBy($user->id);
        $entity3->likeBy($user->id);

        DB::enableQueryLog();
        $entities = Entity::with('dislikes')->get();
        $entitiesIsLiked = [];
        foreach ($entities as $entity) {
            $entitiesIsLiked[] = $entity->isLikedBy();
        }
        $queryLog = DB::getQueryLog();

        $this->assertCount(5, $queryLog);
        $this->assertTrue($entitiesIsLiked[0]);
        $this->assertFalse($entitiesIsLiked[1]);
        $this->assertTrue($entitiesIsLiked[2]);
    }

    /** @test */
    public function it_uses_eager_loaded_dislikes_relation_on_disliked_check()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $entity1 = factory(Entity::class)->create();
        $entity2 = factory(Entity::class)->create();
        $entity3 = factory(Entity::class)->create();
        $entity1->dislikeBy($user->id);
        $entity3->dislikeBy($user->id);

        DB::enableQueryLog();
        $entities = Entity::with('dislikes')->get();
        $entitiesIsDisliked = [];
        foreach ($entities as $entity) {
            $entitiesIsDisliked[] = $entity->isDislikedBy();
        }
        $queryLog = DB::getQueryLog();

        $this->assertCount(2, $queryLog);
        $this->assertTrue($entitiesIsDisliked[0]);
        $this->assertFalse($entitiesIsDisliked[1]);
        $this->assertTrue($entitiesIsDisliked[2]);
    }

    /** @test */
    public function it_uses_eager_loaded_likes_and_dislikes_relation_on_disliked_check()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $entity1 = factory(Entity::class)->create();
        $entity2 = factory(Entity::class)->create();
        $entity3 = factory(Entity::class)->create();
        $entity1->dislikeBy($user->id);
        $entity3->dislikeBy($user->id);

        DB::enableQueryLog();
        $entities = Entity::with('likesAndDislikes')->get();
        $entitiesIsDisliked = [];
        foreach ($entities as $entity) {
            $entitiesIsDisliked[] = $entity->isDislikedBy();
        }
        $queryLog = DB::getQueryLog();

        $this->assertCount(2, $queryLog);
        $this->assertTrue($entitiesIsDisliked[0]);
        $this->assertFalse($entitiesIsDisliked[1]);
        $this->assertTrue($entitiesIsDisliked[2]);
    }

    /** @test */
    public function it_does_not_use_eager_loaded_likes_relation_on_disliked_check()
    {
        $user = factory(User::class)->create();
        $this->actingAs($user);
        $entity1 = factory(Entity::class)->create();
        $entity2 = factory(Entity::class)->create();
        $entity3 = factory(Entity::class)->create();
        $entity1->dislikeBy($user->id);
        $entity3->dislikeBy($user->id);

        DB::enableQueryLog();
        $entities = Entity::with('likes')->get();
        $entitiesIsDisliked = [];
        foreach ($entities as $entity) {
            $entitiesIsDisliked[] = $entity->isDislikedBy();
        }
        $queryLog = DB::getQueryLog();

        $this->assertCount(5, $queryLog);
        $this->assertTrue($entitiesIsDisliked[0]);
        $this->assertFalse($entitiesIsDisliked[1]);
        $this->assertTrue($entitiesIsDisliked[2]);
    }

    /** @test */
    public function it_can_get_dislikes_and_likes_relation()
    {
        $entity = factory(Entity::class)->create();

        $entity->likeBy(1);
        $entity->dislikeBy(2);

        $this->assertInstanceOf(LikeContract::class, $entity->likesAndDislikes->first());
        $this->assertCount(2, $entity->likesAndDislikes);
    }

    /** @test */
    public function it_can_get_likes_minus_dislikes_difference()
    {
        $entity = factory(Entity::class)->create();

        $entity->likeBy(1);
        $entity->dislikeBy(2);
        $entity->dislikeBy(3);

        $this->assertEquals(-1, $entity->likesDiffDislikesCount);
    }

    /** @test */
    public function it_can_sort_entities_desc_by_likes_count()
    {
        $entityA = factory(Entity::class)->create();
        $entityB = factory(Entity::class)->create();
        $entityC = factory(Entity::class)->create();
        $entityD = factory(Entity::class)->create();
        for ($i = 0; $i < 3; $i++) {
            $entityA->likeBy(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 1; $i++) {
            $entityB->likeBy(mt_rand(1, 9999999));
            $entityB->dislikeBy(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 5; $i++) {
            $entityC->likeBy(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 10; $i++) {
            $entityD->likeBy(mt_rand(1, 9999999));
            $entityD->dislikeBy(mt_rand(1, 9999999));
        }

        $sortedEntities = Entity::orderByLikesCount('desc')->get();

        $this->assertSame([
            $entityD->getKey() => '10',
            $entityC->getKey() => '5',
            $entityA->getKey() => '3',
            $entityB->getKey() => '1',
        ], $sortedEntities->pluck('count', 'id')->toArray());
    }

    /** @test */
    public function it_can_sort_entities_asc_by_likes_count()
    {
        $entityA = factory(Entity::class)->create();
        $entityB = factory(Entity::class)->create();
        $entityC = factory(Entity::class)->create();
        $entityD = factory(Entity::class)->create();
        for ($i = 0; $i < 3; $i++) {
            $entityA->likeBy(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 1; $i++) {
            $entityB->likeBy(mt_rand(1, 9999999));
            $entityB->dislikeBy(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 5; $i++) {
            $entityC->likeBy(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 10; $i++) {
            $entityD->likeBy(mt_rand(1, 9999999));
            $entityD->dislikeBy(mt_rand(1, 9999999));
        }

        $sortedEntities = Entity::orderByLikesCount('asc')->get();

        $this->assertSame([
            $entityB->getKey() => '1',
            $entityA->getKey() => '3',
            $entityC->getKey() => '5',
            $entityD->getKey() => '10',
        ], $sortedEntities->pluck('count', 'id')->toArray());
    }

    /** @test */
    public function it_can_get_entities_without_likes_while_sort_them_by_likes_count()
    {
        $entityA = factory(Entity::class)->create();
        $entityB = factory(Entity::class)->create();
        $entityC = factory(Entity::class)->create();
        $entityD = factory(Entity::class)->create();
        for ($i = 0; $i < 3; $i++) {
            $entityA->likeBy(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 10; $i++) {
            $entityD->likeBy(mt_rand(1, 9999999));
            $entityD->dislikeBy(mt_rand(1, 9999999));
        }

        $sortedEntities = Entity::orderByLikesCount('desc')->get();

        $this->assertSame([
            $entityD->getKey() => '10',
            $entityA->getKey() => '3',
            $entityB->getKey() => null,
            $entityC->getKey() => null,
        ], $sortedEntities->pluck('count', 'id')->toArray());
    }

    /** @test */
    public function it_can_sort_entities_desc_by_dislikes_count()
    {
        $entityA = factory(Entity::class)->create();
        $entityB = factory(Entity::class)->create();
        $entityC = factory(Entity::class)->create();
        $entityD = factory(Entity::class)->create();
        for ($i = 0; $i < 3; $i++) {
            $entityA->dislikeBy(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 1; $i++) {
            $entityB->dislikeBy(mt_rand(1, 9999999));
            $entityB->likeBy(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 5; $i++) {
            $entityC->dislikeBy(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 10; $i++) {
            $entityD->dislikeBy(mt_rand(1, 9999999));
            $entityD->likeBy(mt_rand(1, 9999999));
        }

        $sortedEntities = Entity::orderByDislikesCount('desc')->get();

        $this->assertSame([
            $entityD->getKey() => '10',
            $entityC->getKey() => '5',
            $entityA->getKey() => '3',
            $entityB->getKey() => '1',
        ], $sortedEntities->pluck('count', 'id')->toArray());
    }

    /** @test */
    public function it_can_sort_entities_asc_by_dislikes_count()
    {
        $entityA = factory(Entity::class)->create();
        $entityB = factory(Entity::class)->create();
        $entityC = factory(Entity::class)->create();
        $entityD = factory(Entity::class)->create();
        for ($i = 0; $i < 3; $i++) {
            $entityA->dislikeBy(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 1; $i++) {
            $entityB->dislikeBy(mt_rand(1, 9999999));
            $entityB->likes(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 5; $i++) {
            $entityC->dislikeBy(mt_rand(1, 9999999));
            $entityC->likeBy(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 10; $i++) {
            $entityD->dislikeBy(mt_rand(1, 9999999));
        }

        $sortedEntities = Entity::orderByDislikesCount('asc')->get();

        $this->assertSame([
            $entityB->getKey() => '1',
            $entityA->getKey() => '3',
            $entityC->getKey() => '5',
            $entityD->getKey() => '10',
        ], $sortedEntities->pluck('count', 'id')->toArray());
    }

    /** @test */
    public function it_can_get_entities_without_likes_while_sort_them_by_dislikes_count()
    {
        $entityA = factory(Entity::class)->create();
        $entityB = factory(Entity::class)->create();
        $entityC = factory(Entity::class)->create();
        $entityD = factory(Entity::class)->create();
        for ($i = 0; $i < 3; $i++) {
            $entityA->dislikeBy(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 10; $i++) {
            $entityD->likeBy(mt_rand(1, 9999999));
            $entityD->dislikeBy(mt_rand(1, 9999999));
        }

        $sortedEntities = Entity::orderByDislikesCount('desc')->get();

        $this->assertSame([
            $entityD->getKey() => '10',
            $entityA->getKey() => '3',
            $entityB->getKey() => null,
            $entityC->getKey() => null,
        ], $sortedEntities->pluck('count', 'id')->toArray());
    }

    /** @test */
    public function it_can_collect_likers()
    {
        $entity = factory(Entity::class)->create();
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        $entity->likeBy($user1->id);
        $entity->dislikeBy($user2->id);
        $entity->likeBy($user3->id);

        $likers = $entity->collectLikers();

        $this->assertCount(2, $likers);
        $this->assertEquals([$user1->id, $user3->id], $likers->pluck('id')->toArray());
    }

    /** @test */
    public function it_can_collect_dislikers()
    {
        $entity = factory(Entity::class)->create();
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        $entity->dislikeBy($user1->id);
        $entity->likeBy($user2->id);
        $entity->dislikeBy($user3->id);

        $dislikers = $entity->collectDislikers();

        $this->assertCount(2, $dislikers);
        $this->assertEquals([$user1->id, $user3->id], $dislikers->pluck('id')->toArray());
    }
}
