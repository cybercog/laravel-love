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

namespace Cog\Tests\Laravel\Love\Unit\Likeable\Services;

use Cog\Laravel\Love\LikeCounter\Models\LikeCounter;
use Cog\Contracts\Love\Likeable\Services\LikeableService as LikeableServiceContract;
use Cog\Laravel\Love\Likeable\Services\LikeableService;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\Entity;
use Cog\Tests\Laravel\Love\Stubs\Models\EntityWithMorphMap;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class LikeableServiceTest.
 *
 * @package Cog\Tests\Laravel\Love\Unit\Services
 */
class LikeableServiceTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_instantiate_service()
    {
        $service = $this->app->make(LikeableServiceContract::class);

        $this->assertInstanceOf(LikeableService::class, $service);
    }

    /** @test */
    public function it_can_decrement_like_count()
    {
        $service = $this->app->make(LikeableServiceContract::class);
        $entity = factory(Entity::class)->create();
        $entity->like(1);
        $entity->like(2);

        $service->decrementLikesCount($entity);
        $service->decrementLikesCount($entity);

        $this->assertEquals(0, $entity->likesCount);
    }

    /** @test */
    public function it_cannot_decrement_lower_than_zero()
    {
        $service = $this->app->make(LikeableServiceContract::class);
        $entity = factory(Entity::class)->create();

        $service->decrementLikesCount($entity);

        $this->assertEquals(0, $entity->likesCount);
    }

    /** @test */
    public function it_can_increment_like_count()
    {
        $service = $this->app->make(LikeableServiceContract::class);
        $entity = factory(Entity::class)->create();

        $service->incrementLikesCount($entity);
        $service->incrementLikesCount($entity);

        $this->assertEquals(2, $entity->likesCount);
    }

    /** @test */
    public function it_can_remove_like_counters_for_type()
    {
        $service = $this->app->make(LikeableServiceContract::class);
        $entity1 = factory(Entity::class)->create();
        $entity2 = factory(Entity::class)->create();
        $article = factory(Article::class)->create();
        $entity1->like(1);
        $entity2->like(2);
        $article->like(1);

        $service->removeLikeCountersOfType(Entity::class, 'LIKE');

        $likeCounters = LikeCounter::all();

        $this->assertCount(1, $likeCounters);
    }

    /** @test */
    public function it_can_remove_like_counters_for_type_using_morph()
    {
        $service = $this->app->make(LikeableServiceContract::class);
        $entity1 = factory(EntityWithMorphMap::class)->create();
        $entity2 = factory(EntityWithMorphMap::class)->create();
        $article = factory(Article::class)->create();
        $entity1->like(1);
        $entity2->like(2);
        $article->like(1);

        $service->removeLikeCountersOfType('entity-with-morph-map', 'LIKE');

        $likeCounters = LikeCounter::all();

        $this->assertCount(1, $likeCounters);
    }

    /** @test */
    public function it_can_remove_like_counters_for_type_with_morph_using_full_class_name()
    {
        $service = $this->app->make(LikeableServiceContract::class);
        $entity1 = factory(EntityWithMorphMap::class)->create();
        $entity2 = factory(EntityWithMorphMap::class)->create();
        $article = factory(Article::class)->create();
        $entity1->like(1);
        $entity2->like(2);
        $article->like(1);

        $service->removeLikeCountersOfType(EntityWithMorphMap::class, 'LIKE');

        $likeCounters = LikeCounter::all();

        $this->assertCount(1, $likeCounters);
    }

    /** @test */
    public function it_can_remove_model_likes()
    {
        $service = $this->app->make(LikeableServiceContract::class);
        $entity1 = factory(Entity::class)->create();
        $entity2 = factory(Entity::class)->create();
        $entity1->like(1);
        $entity1->like(2);
        $entity2->like(1);
        $entity2->like(4);

        $service->removeModelLikes($entity1, 'LIKE');

        $this->assertEmpty($entity1->likes);
        $this->assertNotEmpty($entity2->likes);
    }

    /** @test */
    public function it_can_remove_like_on_dislike()
    {
        $entity = factory(Entity::class)->create();

        $entity->like(1);
        $entity->dislike(1);

        $this->assertCount(1, $entity->likesAndDislikes);
        $this->assertCount(1, $entity->dislikes);
        $this->assertEquals(1, $entity->dislikesCount);
    }

    /** @test */
    public function it_can_remove_dislike_on_like()
    {
        $entity = factory(Entity::class)->create();

        $entity->dislike(1);
        $entity->like(1);

        $this->assertCount(1, $entity->likesAndDislikes);
        $this->assertCount(1, $entity->likes);
        $this->assertEquals(1, $entity->likesCount);
    }

    /** @test */
    public function it_can_collect_likers_of_entity()
    {
        $entity = factory(Entity::class)->create();
        $user1 = factory(User::class)->create();
        factory(User::class)->create();
        $user3 = factory(User::class)->create();
        $entity->like($user1->id);
        $entity->like($user3->id);

        $likers = app(LikeableServiceContract::class)->collectLikersOf($entity);

        $this->assertCount(2, $likers);
        $this->assertEquals([$user1->id, $user3->id], $likers->pluck('id')->toArray());
    }

    /** @test */
    public function it_can_collect_dislikers_of_entity()
    {
        $entity = factory(Entity::class)->create();
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        $entity->dislike($user1->id);
        $entity->like($user2->id);
        $entity->dislike($user3->id);

        $dislikers = app(LikeableServiceContract::class)->collectDislikersOf($entity);

        $this->assertCount(2, $dislikers);
        $this->assertEquals([$user1->id, $user3->id], $dislikers->pluck('id')->toArray());
    }
}
