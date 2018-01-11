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

namespace Cog\Tests\Laravel\Love\Unit\Like\Observers;

use Cog\Contracts\Love\Like\Models\Like as LikeContract;
use Cog\Contracts\Love\Likeable\Services\LikeableService;
use Cog\Laravel\Love\Like\Enums\LikeType;
use Cog\Laravel\Love\Like\Observers\LikeObserver;
use Cog\Laravel\Love\Likeable\Events\LikeableWasDisliked;
use Cog\Laravel\Love\Likeable\Events\LikeableWasLiked;
use Cog\Laravel\Love\Likeable\Events\LikeableWasUndisliked;
use Cog\Laravel\Love\Likeable\Events\LikeableWasUnliked;
use Cog\Tests\Laravel\Love\Stubs\Models\Entity;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Mockery;

/**
 * Class LikeObserverTest.
 *
 * @package Cog\Tests\Laravel\Love\Unit\Like\Observers
 */
class LikeObserverTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_fires_model_was_liked_event_on_like_create()
    {
        $this->expectsEvents(LikeableWasLiked::class);

        $like = Mockery::mock(LikeContract::class);
        $like->type_id = LikeType::LIKE;
        $like->user_id = factory(User::class)->create();
        $like->likeable = factory(Entity::class)->create();
        $likeObserver = new LikeObserver();

        $likeObserver->created($like);
    }

    /** @test */
    public function it_fires_model_was_disliked_event_on_dislike_create()
    {
        $this->expectsEvents(LikeableWasDisliked::class);

        $like = Mockery::mock(LikeContract::class);
        $like->type_id = LikeType::DISLIKE;
        $like->user_id = factory(User::class)->create();
        $like->likeable = factory(Entity::class)->create();
        $likeObserver = new LikeObserver();

        $likeObserver->created($like);
    }

    /** @test */
    public function it_fires_model_was_unliked_event_on_like_delete()
    {
        $this->expectsEvents(LikeableWasUnliked::class);

        $like = Mockery::mock(LikeContract::class);
        $like->type_id = LikeType::LIKE;
        $like->user_id = factory(User::class)->create();
        $like->likeable = factory(Entity::class)->create();
        $likeObserver = new LikeObserver();

        $likeObserver->deleted($like);
    }

    /** @test */
    public function it_fires_model_was_undisliked_event_on_dislike_delete()
    {
        $this->expectsEvents(LikeableWasUndisliked::class);

        $like = Mockery::mock(LikeContract::class);
        $like->type_id = LikeType::DISLIKE;
        $like->user_id = factory(User::class)->create();
        $like->likeable = factory(Entity::class)->create();
        $likeObserver = new LikeObserver();

        $likeObserver->deleted($like);
    }

    /** @test */
    public function it_increment_likeable_likes_count_on_like_model_created()
    {
        $likeable = factory(Entity::class)->create();
        $likeableService = Mockery::mock(LikeableService::class);
        $like = Mockery::mock(LikeContract::class);
        $like->type_id = LikeType::LIKE;
        $like->user_id = factory(User::class)->create();
        $like->likeable = $likeable;
        $likeObserver = new LikeObserver();
        $likeableService->shouldReceive('incrementLikesCount');

        $likeObserver->created($like);

        $this->assertSame(1, $likeable->likesCount);
    }

    /** @test */
    public function it_increment_likeable_dislikes_count_on_like_model_created()
    {
        $likeable = factory(Entity::class)->create();
        $likeableService = Mockery::mock(LikeableService::class);
        $like = Mockery::mock(LikeContract::class);
        $like->type_id = LikeType::DISLIKE;
        $like->user_id = factory(User::class)->create();
        $like->likeable = $likeable;
        $likeObserver = new LikeObserver();
        $likeableService->shouldReceive('incrementDislikesCount');

        $likeObserver->created($like);

        $this->assertSame(1, $likeable->dislikesCount);
    }

    /** @test */
    public function it_decrement_likeable_likes_count_on_like_model_deleted()
    {
        $likeable = factory(Entity::class)->create();
        $likeable->likeBy(1);
        $likeableService = Mockery::mock(LikeableService::class);
        $like = Mockery::mock(LikeContract::class);
        $like->type_id = LikeType::LIKE;
        $like->user_id = factory(User::class)->create();
        $like->likeable = $likeable;
        $likeObserver = new LikeObserver();
        $likeableService->shouldReceive('decrementLikesCount');

        $likeObserver->deleted($like);

        $this->assertSame(0, $likeable->likesCount);
    }

    /** @test */
    public function it_decrement_likeable_dislikes_count_on_like_model_deleted()
    {
        $likeable = factory(Entity::class)->create();
        $likeable->dislikeBy(1);
        $likeableService = Mockery::mock(LikeableService::class);
        $like = Mockery::mock(LikeContract::class);
        $like->type_id = LikeType::DISLIKE;
        $like->user_id = factory(User::class)->create();
        $like->likeable = $likeable;
        $likeObserver = new LikeObserver();
        $likeableService->shouldReceive('decrementDislikesCount');

        $likeObserver->deleted($like);

        $this->assertSame(0, $likeable->dislikesCount);
    }
}
