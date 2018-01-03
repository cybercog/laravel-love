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
use Cog\Laravel\Love\Like\Enums\LikeType;
use Cog\Laravel\Love\Like\Observers\LikeObserver;
use Cog\Laravel\Love\Likeable\Events\ModelWasDisliked;
use Cog\Laravel\Love\Likeable\Events\ModelWasLiked;
use Cog\Laravel\Love\Likeable\Events\ModelWasUndisliked;
use Cog\Laravel\Love\Likeable\Events\ModelWasUnliked;
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
        $this->expectsEvents(ModelWasLiked::class);

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
        $this->expectsEvents(ModelWasDisliked::class);

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
        $this->expectsEvents(ModelWasUnliked::class);

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
        $this->expectsEvents(ModelWasUndisliked::class);

        $like = Mockery::mock(LikeContract::class);
        $like->type_id = LikeType::DISLIKE;
        $like->user_id = factory(User::class)->create();
        $like->likeable = factory(Entity::class)->create();
        $likeObserver = new LikeObserver();

        $likeObserver->deleted($like);
    }

    // TODO: Add test that `incrementLikesCount` was called
    // TODO: Add test that `incrementDislikesCount` was called
    // TODO: Add test that `decrementLikesCount` was called
    // TODO: Add test that `decrementDislikesCount` was called
}
