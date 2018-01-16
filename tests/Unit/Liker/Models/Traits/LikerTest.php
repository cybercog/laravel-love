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

namespace Cog\Tests\Laravel\Love\Unit\Liker\Models\Traits;

use Cog\Tests\Laravel\Love\Stubs\Models\Entity;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class LikerTest.
 *
 * @package Cog\Tests\Laravel\Love\Unit\Liker\Models\Traits
 */
class LikerTest extends TestCase
{
    use DatabaseTransactions;

    /* Likes */

    /** @test */
    public function it_can_like_likeable()
    {
        $entity = factory(Entity::class)->create();

        $user = factory(User::class)->create();
        $this->actingAs($user);

        $user->like($entity);

        $this->assertEquals(1, $entity->likesCount);
        $this->assertEquals($user->id, $entity->likes->first()->user_id);
    }

    /** @test */
    public function it_can_like_likeable_by_other_user()
    {
        $entity = factory(Entity::class)->create();

        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $this->actingAs($user1);

        $user2->like($entity);

        $this->assertEquals(1, $entity->likesCount);
        $this->assertEquals($user2->id, $entity->likes->first()->user_id);
    }

    /** @test */
    public function it_can_like_many_likeables()
    {
        $user = factory(User::class)->create();
        $entity1 = factory(Entity::class)->create();
        $entity2 = factory(Entity::class)->create();

        $user->like($entity1);
        $user->like($entity2);

        $this->assertEquals(1, $entity1->likesCount);
        $this->assertEquals($user->id, $entity1->likes->first()->user_id);
        $this->assertEquals(1, $entity2->likesCount);
        $this->assertEquals($user->id, $entity2->likes->first()->user_id);
    }

    /** @test */
    public function it_cannot_duplicate_likes()
    {
        $user = factory(User::class)->create();
        $entity = factory(Entity::class)->create();

        $user->like($entity);
        $user->like($entity);

        $this->assertEquals(1, $entity->likesCount);
    }

    /** @test */
    public function it_can_unlike_likeable()
    {
        $user = factory(User::class)->create();
        $entity = factory(Entity::class)->create();
        $user->like($entity);

        $user->unlike($entity);

        $this->assertEquals(0, $entity->likesCount);
    }

    /** @test */
    public function it_cannot_unlike_likeable_if_not_liked()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $entity = factory(Entity::class)->create();
        $user2->like($entity);

        $user->unlike($entity);

        $this->assertEquals(1, $entity->likesCount);
    }

    /** @test */
    public function it_can_add_like_with_toggle()
    {
        $user = factory(User::class)->create();
        $entity = factory(Entity::class)->create();

        $user->toggleLike($entity);

        $this->assertEquals(1, $entity->likesCount);
    }

    /** @test */
    public function it_can_remove_like_with_toggle()
    {
        $user = factory(User::class)->create();
        $entity = factory(Entity::class)->create();
        $user->like($entity);

        $user->toggleLike($entity);

        $this->assertEquals(0, $entity->likesCount);
    }

    /** @test */
    public function it_can_check_if_liker_has_liked_likeable()
    {
        $user = factory(User::class)->create();
        $entity = factory(Entity::class)->create();
        $user->like($entity);

        $this->assertTrue($user->hasLiked($entity));
    }

    /** @test */
    public function it_can_check_if_liker_has_not_liked_likeable()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $entity = factory(Entity::class)->create();
        $user->like($entity);

        $this->assertFalse($user2->hasLiked($entity));
    }

    /* Dislikes */

    /** @test */
    public function it_can_dislike_likeable()
    {
        $entity = factory(Entity::class)->create();

        $user = factory(User::class)->create();
        $this->actingAs($user);

        $user->dislike($entity);

        $this->assertEquals(1, $entity->dislikesCount);
        $this->assertEquals($user->id, $entity->dislikes->first()->user_id);
    }

    /** @test */
    public function it_can_dislike_likeable_by_other_user()
    {
        $entity = factory(Entity::class)->create();

        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $this->actingAs($user1);

        $user2->dislike($entity);

        $this->assertEquals(1, $entity->dislikesCount);
        $this->assertEquals($user2->id, $entity->dislikes->first()->user_id);
    }

    /** @test */
    public function it_can_dislike_many_likeables()
    {
        $user = factory(User::class)->create();
        $entity1 = factory(Entity::class)->create();
        $entity2 = factory(Entity::class)->create();

        $user->dislike($entity1);
        $user->dislike($entity2);

        $this->assertEquals(1, $entity1->dislikesCount);
        $this->assertEquals($user->id, $entity1->dislikes->first()->user_id);
        $this->assertEquals(1, $entity2->dislikesCount);
        $this->assertEquals($user->id, $entity2->dislikes->first()->user_id);
    }

    /** @test */
    public function it_cannot_duplicate_dislikes()
    {
        $user = factory(User::class)->create();
        $entity = factory(Entity::class)->create();

        $user->dislike($entity);
        $user->dislike($entity);

        $this->assertEquals(1, $entity->dislikesCount);
    }

    /** @test */
    public function it_can_undislike_likeable()
    {
        $user = factory(User::class)->create();
        $entity = factory(Entity::class)->create();
        $user->dislike($entity);

        $user->undislike($entity);

        $this->assertEquals(0, $entity->likesCount);
    }

    /** @test */
    public function it_cannot_undislike_likeable_if_not_disliked()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $entity = factory(Entity::class)->create();
        $user2->dislike($entity);

        $user->undislike($entity);

        $this->assertEquals(1, $entity->dislikesCount);
    }

    /** @test */
    public function it_can_add_dislike_with_toggle()
    {
        $user = factory(User::class)->create();
        $entity = factory(Entity::class)->create();

        $user->toggleDislike($entity);

        $this->assertEquals(1, $entity->dislikesCount);
    }

    /** @test */
    public function it_can_remove_dislike_with_toggle()
    {
        $user = factory(User::class)->create();
        $entity = factory(Entity::class)->create();
        $user->dislike($entity);

        $user->toggleDislike($entity);

        $this->assertEquals(0, $entity->dislikesCount);
    }

    /** @test */
    public function it_can_check_if_liker_has_disliked_likeable()
    {
        $user = factory(User::class)->create();
        $entity = factory(Entity::class)->create();
        $user->dislike($entity);

        $this->assertTrue($user->hasDisliked($entity));
    }

    /** @test */
    public function it_can_check_if_liker_has_not_disliked_likeable()
    {
        $user = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $entity = factory(Entity::class)->create();
        $user->dislike($entity);

        $this->assertFalse($user2->hasDisliked($entity));
    }
}
