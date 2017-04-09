<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Likeable\Tests\Unit\Services;

use Cog\Likeable\Models\LikeCounter;
use Cog\Likeable\Services\LikeableService as LikeableServiceContract;
use Cog\Likeable\Tests\Stubs\Models\Article;
use Cog\Likeable\Tests\Stubs\Models\Entity;
use Cog\Likeable\Tests\Stubs\Models\EntityWithMorphMap;
use Cog\Likeable\Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class LikeableServiceTest.
 *
 * @package Cog\Likeable\Tests\Unit\Services
 */
class LikeableServiceTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_instantiate_service()
    {
        $service = $this->app->make(LikeableServiceContract::class);

        $this->assertInstanceOf(LikeableServiceContract::class, $service);
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

        $service->removeLikeCountersOfType(Entity::class, 'like');

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

        $service->removeLikeCountersOfType('entity-with-morph-map', 'like');

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

        $service->removeLikeCountersOfType(EntityWithMorphMap::class, 'like');

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

        $service->removeModelLikes($entity1, 'like');

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
    public function it_can_sort_entities_desc_by_likes_count()
    {
        $entityA = factory(Entity::class)->create();
        $entityB = factory(Entity::class)->create();
        $entityC = factory(Entity::class)->create();
        $entityD = factory(Entity::class)->create();
        for ($i = 0; $i < 3; $i++) {
            $entityA->like(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 1; $i++) {
            $entityB->like(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 5; $i++) {
            $entityC->like(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 10; $i++) {
            $entityD->like(mt_rand(1, 9999999));
        }

        $service = app(LikeableServiceContract::class);
        $sortedEntities = $service->scopeSortedByLikesCount(Entity::query(), new Entity())->get();

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
            $entityA->like(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 1; $i++) {
            $entityB->like(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 5; $i++) {
            $entityC->like(mt_rand(1, 9999999));
        }
        for ($i = 0; $i < 10; $i++) {
            $entityD->like(mt_rand(1, 9999999));
        }

        $service = app(LikeableServiceContract::class);
        $sortedEntities = $service->scopeSortedByLikesCount(Entity::query(), 'asc')->get();

        $this->assertSame([
            $entityB->getKey() => '1',
            $entityA->getKey() => '3',
            $entityC->getKey() => '5',
            $entityD->getKey() => '10',
        ], $sortedEntities->pluck('count', 'id')->toArray());
    }
}
