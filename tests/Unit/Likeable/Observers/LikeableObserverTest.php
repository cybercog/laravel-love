<?php

/*
 * This file is part of Laravel Love.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Tests\Laravel\Love\Unit\Likeable\Observers;

use Cog\Contracts\Love\Likeable\Models\Likeable as LikeableContract;
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
    public function it_can_call_remove_likes_on_model_deleted()
    {
        $observer = new LikeableObserver;
        $model = Mockery::mock(LikeableContract::class);
        $model->shouldReceive('removeLikes');
        $observer->deleted($model);
    }

    /** @test */
    public function it_can_omit_call_remove_likes_on_model_deleted()
    {
        $observer = new LikeableObserver;
        $model = Mockery::mock(LikeableContract::class);
        $model->removeLikesOnDelete = false;
        $model->shouldNotHaveReceived('removeLikes');
        $observer->deleted($model);
    }

    /** @test */
    public function it_can_delete_likes_with_entity_delete()
    {
        $entity1 = factory(Entity::class)->create();
        $entity2 = factory(Entity::class)->create();

        $entity1->like(1);
        $entity1->like(7);
        $entity1->like(8);
        $entity2->like(1);
        $entity2->like(2);
        $entity2->like(3);
        $entity2->like(4);

        $entity1Likes = $entity1->likes;

        $entity1->delete();

        $entity1Likes = Like::whereIn('id', $entity1Likes->pluck('id'))->get();
        $likeCounter = LikeCounter::all();

        $this->assertCount(0, $entity1Likes);
        $this->assertCount(1, $likeCounter);
    }
}
