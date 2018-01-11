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

namespace Cog\Tests\Laravel\Love\Unit\Likeable\Observers;

use Cog\Contracts\Love\Likeable\Services\LikeableService as LikeableServiceContract;
use Cog\Laravel\Love\Like\Models\Like;
use Cog\Laravel\Love\LikeCounter\Models\LikeCounter;
use Cog\Laravel\Love\Likeable\Observers\LikeableObserver;
use Cog\Tests\Laravel\Love\Stubs\Models\Entity;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;

/**
 * Class LikeableObserverTest.
 *
 * @package Cog\Tests\Laravel\Love\Unit\Likeable\Observers
 */
class LikeableObserverTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_remove_likes_on_likeable_delete()
    {
        $likeable = factory(Entity::class)->create();
        $likeable->likeBy(1);
        $likeableService = Mockery::mock(LikeableServiceContract::class);
        $likeableService->shouldReceive('removeModelLikes');
        $likeableObserver = new LikeableObserver();

        $likeableObserver->deleted($likeable);

        $this->assertEmpty($likeable->likes);
    }

    /** @test */
    public function it_can_omit_remove_likes_on_likeable_delete()
    {
        $likeable = factory(Entity::class)->create();
        $likeable->likeBy(1);
        $likeable->removeLikesOnDelete = false;
        $likeableService = Mockery::mock(LikeableServiceContract::class);
        $likeableService->shouldNotHaveReceived('removeModelLikes');
        $likeableObserver = new LikeableObserver();

        $likeableObserver->deleted($likeable);

        $this->assertCount(1, $likeable->likes);
    }

    /** @test */
    public function it_can_delete_likes_with_entity_delete()
    {
        $entity1 = factory(Entity::class)->create();
        $entity2 = factory(Entity::class)->create();

        $entity1->likeBy(1);
        $entity1->likeBy(7);
        $entity1->likeBy(8);
        $entity2->likeBy(1);
        $entity2->likeBy(2);
        $entity2->likeBy(3);
        $entity2->likeBy(4);

        $entity1Likes = $entity1->likes;

        $entity1->delete();

        $entity1Likes = Like::whereIn('id', $entity1Likes->pluck('id'))->get();
        $likeCounter = LikeCounter::all();

        $this->assertCount(0, $entity1Likes);
        $this->assertCount(1, $likeCounter);
    }
}
