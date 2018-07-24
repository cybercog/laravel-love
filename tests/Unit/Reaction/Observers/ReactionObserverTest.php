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

namespace Cog\Tests\Laravel\Love\Unit\Reaction\Observers;

use Cog\Contracts\Love\Like\Models\Like as LikeContract;
use Cog\Contracts\Love\Likeable\Services\LikeableService;
use Cog\Laravel\Love\Like\Enums\LikeType;
use Cog\Laravel\Love\Like\Observers\LikeObserver;
use Cog\Laravel\Love\Likeable\Events\LikeableWasDisliked;
use Cog\Laravel\Love\Likeable\Events\LikeableWasLiked;
use Cog\Laravel\Love\Likeable\Events\LikeableWasUndisliked;
use Cog\Laravel\Love\Likeable\Events\LikeableWasUnliked;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionCounter\Services\ReactionCounterService;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\Reaction\Observers\ReactionObserver;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\Entity;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class ReactionObserverTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_fires_model_was_liked_event_on_like_create()
    {
        $this->markTestSkipped('Not implemented yet.');

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
        $this->markTestSkipped('Not implemented yet.');

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
        $this->markTestSkipped('Not implemented yet.');

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
        $this->markTestSkipped('Not implemented yet.');

        $this->expectsEvents(LikeableWasUndisliked::class);

        $like = Mockery::mock(LikeContract::class);
        $like->type_id = LikeType::DISLIKE;
        $like->user_id = factory(User::class)->create();
        $like->likeable = factory(Entity::class)->create();
        $likeObserver = new LikeObserver();

        $likeObserver->deleted($like);
    }

    /** @test */
    public function it_increment_reactions_count_on_reaction_created()
    {
        $this->markTestSkipped('Not implemented yet.');

        $reactant = factory(Reactant::class)->create();
        $reactionType = factory(ReactionType::class)->create();
        $counter = factory(ReactionCounter::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $this->assertSame(1, $counter->fresh()->count);
    }

    /** @test */
    public function it_decrement_reactions_count_on_reaction_deleted()
    {
        $this->markTestSkipped('Not implemented yet.');

        $reactant = factory(Reactant::class)->create();
        $reactionType = factory(ReactionType::class)->create();
        $counter = factory(ReactionCounter::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);
        $reactions = factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $reactions->get(0)->delete();

        $this->assertSame(1, $counter->fresh()->count);
    }
}
