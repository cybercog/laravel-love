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

namespace Cog\Tests\Laravel\Love\Unit\Console\Commands;

use Cog\Contracts\Love\Reactable\Exceptions\ReactableInvalid;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeContract;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\Bot;
use Cog\Tests\Laravel\Love\Stubs\Models\Entity;
use Cog\Tests\Laravel\Love\Stubs\Models\EntityWithMorphMap;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class RecountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (starts_with($this->app->version(), '5.7')) {
            $this->withoutMockingConsoleOutput();
        }
    }

    /** @test */
    public function it_can_recount_all_models_reactions_count_of_type_like_when_counters_not_exist(): void
    {
        $likeType = factory(ReactionType::class)->create([
            'name' => 'Like',
            'weight' => 2,
        ]);
        $dislikeType = factory(ReactionType::class)->create([
            'name' => 'Dislike',
            'weight' => -2,
        ]);
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reacter3 = factory(Reacter::class)->create();
        $reacter4 = factory(Reacter::class)->create();
        $entity1 = factory(Entity::class)->create()->getLoveReactant();
        $entity2 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $entity3 = factory(Article::class)->create()->getLoveReactant();
        $reacter1->reactTo($entity1, $likeType);
        $reacter1->reactTo($entity2, $likeType);
        $reacter1->reactTo($entity3, $dislikeType);
        $reacter2->reactTo($entity1, $likeType);
        $reacter2->reactTo($entity2, $dislikeType);
        $reacter2->reactTo($entity3, $likeType);
        $reacter3->reactTo($entity1, $likeType);
        $reacter3->reactTo($entity2, $likeType);
        $reacter3->reactTo($entity3, $likeType);
        $reacter4->reactTo($entity3, $dislikeType);
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $counters = ReactionCounter::query()->count();
        $this->assertSame(6, $counters);
        $this->assertSame(3, $this->reactionsCount($entity1, $likeType));
        $this->assertSame(2, $this->reactionsCount($entity2, $likeType));
        $this->assertSame(2, $this->reactionsCount($entity3, $likeType));
        $this->assertSame(0, $this->reactionsCount($entity1, $dislikeType));
        $this->assertSame(0, $this->reactionsCount($entity2, $dislikeType));
        $this->assertSame(0, $this->reactionsCount($entity3, $dislikeType));
    }

    /** @test */
    public function it_can_recount_all_models_reactions_weight_of_type_like_when_counters_not_exist(): void
    {
        $likeType = factory(ReactionType::class)->create([
            'name' => 'Like',
            'weight' => 2,
        ]);
        $dislikeType = factory(ReactionType::class)->create([
            'name' => 'Dislike',
            'weight' => -2,
        ]);
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reacter3 = factory(Reacter::class)->create();
        $reacter4 = factory(Reacter::class)->create();
        $entity1 = factory(Entity::class)->create()->getLoveReactant();
        $entity2 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $entity3 = factory(Article::class)->create()->getLoveReactant();
        $reacter1->reactTo($entity1, $likeType);
        $reacter1->reactTo($entity2, $likeType);
        $reacter1->reactTo($entity3, $dislikeType);
        $reacter2->reactTo($entity1, $likeType);
        $reacter2->reactTo($entity2, $dislikeType);
        $reacter2->reactTo($entity3, $likeType);
        $reacter3->reactTo($entity1, $likeType);
        $reacter3->reactTo($entity2, $likeType);
        $reacter3->reactTo($entity3, $likeType);
        $reacter4->reactTo($entity3, $dislikeType);
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $counters = ReactionCounter::query()->count();
        $this->assertSame(6, $counters);
        $this->assertSame(6, $this->reactionsWeight($entity1, $likeType));
        $this->assertSame(4, $this->reactionsWeight($entity2, $likeType));
        $this->assertSame(4, $this->reactionsWeight($entity3, $likeType));
        $this->assertSame(0, $this->reactionsWeight($entity1, $dislikeType));
        $this->assertSame(0, $this->reactionsWeight($entity2, $dislikeType));
        $this->assertSame(0, $this->reactionsWeight($entity3, $dislikeType));
    }

    /** @test */
    public function it_can_recount_all_models_likes_if_counters_not_truncated(): void
    {
        $likeType = factory(ReactionType::class)->create([
            'name' => 'Like',
            'weight' => 2,
        ]);
        $dislikeType = factory(ReactionType::class)->create([
            'name' => 'Dislike',
            'weight' => -2,
        ]);
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reacter3 = factory(Reacter::class)->create();
        $reacter4 = factory(Reacter::class)->create();
        $entity1 = factory(Entity::class)->create()->getLoveReactant();
        $entity2 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $entity3 = factory(Article::class)->create()->getLoveReactant();

        $reacter1->reactTo($entity1, $likeType);
        $reacter1->reactTo($entity2, $likeType);
        $reacter1->reactTo($entity3, $dislikeType);
        $reacter2->reactTo($entity1, $likeType);
        $reacter2->reactTo($entity2, $dislikeType);
        $reacter2->reactTo($entity3, $likeType);
        $reacter3->reactTo($entity1, $likeType);
        $reacter3->reactTo($entity2, $likeType);
        $reacter3->reactTo($entity3, $likeType);
        $reacter4->reactTo($entity3, $dislikeType);

        $status = $this->artisan('love:recount', [
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);

        $counters = ReactionCounter::query()->count();
        $this->assertSame(6, $counters);
        $this->assertSame(3, $this->reactionsCount($entity1, $likeType));
        $this->assertSame(2, $this->reactionsCount($entity2, $likeType));
        $this->assertSame(2, $this->reactionsCount($entity3, $likeType));
        $this->assertSame(0, $this->reactionsCount($entity1, $dislikeType));
        $this->assertSame(1, $this->reactionsCount($entity2, $dislikeType));
        $this->assertSame(2, $this->reactionsCount($entity3, $dislikeType));
    }

    /** @test */
    public function it_can_recount_model_likes(): void
    {
        $likeType = factory(ReactionType::class)->create([
            'name' => 'Like',
            'weight' => 2,
        ]);
        $dislikeType = factory(ReactionType::class)->create([
            'name' => 'Dislike',
            'weight' => -2,
        ]);
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reacter3 = factory(Reacter::class)->create();
        $reacter4 = factory(Reacter::class)->create();
        $entity1 = factory(Entity::class)->create()->getLoveReactant();
        $entity2 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $entity3 = factory(Entity::class)->create()->getLoveReactant();

        $reacter1->reactTo($entity1, $likeType);
        $reacter1->reactTo($entity2, $likeType);
        $reacter1->reactTo($entity3, $dislikeType);
        $reacter2->reactTo($entity1, $likeType);
        $reacter2->reactTo($entity2, $dislikeType);
        $reacter2->reactTo($entity3, $likeType);
        $reacter3->reactTo($entity1, $likeType);
        $reacter3->reactTo($entity2, $likeType);
        $reacter3->reactTo($entity3, $likeType);
        $reacter4->reactTo($entity3, $dislikeType);
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'model' => Entity::class,
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $this->assertSame(6, ReactionCounter::query()->count());
        $this->assertSame(3, $this->reactionsCount($entity1, $likeType));
        $this->assertSame(0, $this->reactionsCount($entity2, $likeType));
        $this->assertSame(2, $this->reactionsCount($entity3, $likeType));
        $this->assertSame(0, $this->reactionsCount($entity1, $dislikeType));
        $this->assertSame(0, $this->reactionsCount($entity2, $dislikeType));
        $this->assertSame(0, $this->reactionsCount($entity3, $dislikeType));
    }

    /** @test */
    public function it_can_recount_model_likes_using_morph_map(): void
    {
        $likeType = factory(ReactionType::class)->create([
            'name' => 'Like',
            'weight' => 2,
        ]);
        $dislikeType = factory(ReactionType::class)->create([
            'name' => 'Dislike',
            'weight' => -2,
        ]);
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reacter3 = factory(Reacter::class)->create();
        $reacter4 = factory(Reacter::class)->create();
        $entity1 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $entity2 = factory(Entity::class)->create()->getLoveReactant();
        $entity3 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $reacter1->reactTo($entity1, $likeType);
        $reacter1->reactTo($entity2, $likeType);
        $reacter1->reactTo($entity3, $dislikeType);
        $reacter2->reactTo($entity1, $likeType);
        $reacter2->reactTo($entity2, $dislikeType);
        $reacter2->reactTo($entity3, $likeType);
        $reacter3->reactTo($entity1, $likeType);
        $reacter3->reactTo($entity2, $likeType);
        $reacter3->reactTo($entity3, $likeType);
        $reacter4->reactTo($entity3, $dislikeType);
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'model' => 'entity-with-morph-map',
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $this->assertSame(6, ReactionCounter::query()->count());
        $this->assertSame(3, $this->reactionsCount($entity1, $likeType));
        $this->assertSame(0, $this->reactionsCount($entity2, $likeType));
        $this->assertSame(2, $this->reactionsCount($entity3, $likeType));
        $this->assertSame(0, $this->reactionsCount($entity1, $dislikeType));
        $this->assertSame(0, $this->reactionsCount($entity2, $dislikeType));
        $this->assertSame(0, $this->reactionsCount($entity3, $dislikeType));
    }

    /** @test */
    public function it_can_recount_model_likes_with_morph_map_using_full_class_name(): void
    {
        $likeType = factory(ReactionType::class)->create([
            'name' => 'Like',
            'weight' => 2,
        ]);
        $dislikeType = factory(ReactionType::class)->create([
            'name' => 'Dislike',
            'weight' => -2,
        ]);
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reacter3 = factory(Reacter::class)->create();
        $reacter4 = factory(Reacter::class)->create();
        $entity1 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $entity2 = factory(Entity::class)->create()->getLoveReactant();
        $entity3 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $reacter1->reactTo($entity1, $likeType);
        $reacter1->reactTo($entity2, $likeType);
        $reacter1->reactTo($entity3, $dislikeType);
        $reacter2->reactTo($entity1, $likeType);
        $reacter2->reactTo($entity2, $dislikeType);
        $reacter2->reactTo($entity3, $likeType);
        $reacter3->reactTo($entity1, $likeType);
        $reacter3->reactTo($entity2, $likeType);
        $reacter3->reactTo($entity3, $likeType);
        $reacter4->reactTo($entity3, $dislikeType);

        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'model' => EntityWithMorphMap::class,
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $this->assertSame(6, ReactionCounter::query()->count());
        $this->assertSame(3, $this->reactionsCount($entity1, $likeType));
        $this->assertSame(0, $this->reactionsCount($entity2, $likeType));
        $this->assertSame(2, $this->reactionsCount($entity3, $likeType));
        $this->assertSame(0, $this->reactionsCount($entity1, $dislikeType));
        $this->assertSame(0, $this->reactionsCount($entity2, $dislikeType));
        $this->assertSame(0, $this->reactionsCount($entity3, $dislikeType));
    }

    /** @test */
    public function it_can_recount_all_models_all_like_types(): void
    {
        $likeType = factory(ReactionType::class)->create([
            'name' => 'Like',
            'weight' => 2,
        ]);
        $dislikeType = factory(ReactionType::class)->create([
            'name' => 'Dislike',
            'weight' => -2,
        ]);
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reacter3 = factory(Reacter::class)->create();
        $reacter4 = factory(Reacter::class)->create();
        $entity1 = factory(Entity::class)->create()->getLoveReactant();
        $entity2 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $entity3 = factory(Article::class)->create()->getLoveReactant();
        $reacter1->reactTo($entity1, $likeType);
        $reacter1->reactTo($entity2, $likeType);
        $reacter1->reactTo($entity3, $dislikeType);
        $reacter2->reactTo($entity1, $likeType);
        $reacter2->reactTo($entity2, $dislikeType);
        $reacter2->reactTo($entity3, $likeType);
        $reacter3->reactTo($entity1, $likeType);
        $reacter3->reactTo($entity2, $likeType);
        $reacter3->reactTo($entity3, $likeType);
        $reacter4->reactTo($entity3, $dislikeType);
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount');

        $this->assertSame(0, $status);
        $counters = ReactionCounter::query()->count();
        $this->assertSame(6, $counters);
        $this->assertSame(3, $this->reactionsCount($entity1, $likeType));
        $this->assertSame(2, $this->reactionsCount($entity2, $likeType));
        $this->assertSame(2, $this->reactionsCount($entity3, $likeType));
        $this->assertSame(0, $this->reactionsCount($entity1, $dislikeType));
        $this->assertSame(1, $this->reactionsCount($entity2, $dislikeType));
        $this->assertSame(2, $this->reactionsCount($entity3, $dislikeType));
    }

    /** @test */
    public function it_can_recount_model_all_like_types(): void
    {
        $likeType = factory(ReactionType::class)->create([
            'name' => 'Like',
            'weight' => 2,
        ]);
        $dislikeType = factory(ReactionType::class)->create([
            'name' => 'Dislike',
            'weight' => -2,
        ]);
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reacter3 = factory(Reacter::class)->create();
        $reacter4 = factory(Reacter::class)->create();
        $entity1 = factory(Entity::class)->create()->getLoveReactant();
        $entity2 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $entity3 = factory(Entity::class)->create()->getLoveReactant();
        $reacter1->reactTo($entity1, $likeType);
        $reacter1->reactTo($entity2, $likeType);
        $reacter1->reactTo($entity3, $dislikeType);
        $reacter2->reactTo($entity1, $likeType);
        $reacter2->reactTo($entity2, $dislikeType);
        $reacter2->reactTo($entity3, $likeType);
        $reacter3->reactTo($entity1, $likeType);
        $reacter3->reactTo($entity2, $likeType);
        $reacter3->reactTo($entity3, $likeType);
        $reacter4->reactTo($entity3, $dislikeType);
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'model' => Entity::class,
        ]);

        $this->assertSame(0, $status);
        $counters = ReactionCounter::query()->count();
        $this->assertSame(6, $counters);
        $this->assertSame(3, $this->reactionsCount($entity1, $likeType));
        $this->assertSame(0, $this->reactionsCount($entity2, $likeType));
        $this->assertSame(2, $this->reactionsCount($entity3, $likeType));
        $this->assertSame(0, $this->reactionsCount($entity1, $dislikeType));
        $this->assertSame(0, $this->reactionsCount($entity2, $dislikeType));
        $this->assertSame(2, $this->reactionsCount($entity3, $dislikeType));
    }

    /** @test */
    public function it_can_recount_model_all_like_types_using_morph_map(): void
    {
        $likeType = factory(ReactionType::class)->create([
            'name' => 'Like',
            'weight' => 2,
        ]);
        $dislikeType = factory(ReactionType::class)->create([
            'name' => 'Dislike',
            'weight' => -2,
        ]);
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reacter3 = factory(Reacter::class)->create();
        $reacter4 = factory(Reacter::class)->create();
        $entity1 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $entity2 = factory(Entity::class)->create()->getLoveReactant();
        $entity3 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $reacter1->reactTo($entity1, $likeType);
        $reacter1->reactTo($entity2, $likeType);
        $reacter1->reactTo($entity3, $dislikeType);
        $reacter2->reactTo($entity1, $likeType);
        $reacter2->reactTo($entity2, $dislikeType);
        $reacter2->reactTo($entity3, $likeType);
        $reacter3->reactTo($entity1, $likeType);
        $reacter3->reactTo($entity2, $likeType);
        $reacter3->reactTo($entity3, $likeType);
        $reacter4->reactTo($entity3, $dislikeType);

        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'model' => 'entity-with-morph-map',
        ]);

        $counters = ReactionCounter::query()->count();
        $this->assertSame(0, $status);
        $this->assertSame(6, $counters);
        $this->assertSame(3, $this->reactionsCount($entity1, $likeType));
        $this->assertSame(0, $this->reactionsCount($entity2, $likeType));
        $this->assertSame(2, $this->reactionsCount($entity3, $likeType));
        $this->assertSame(0, $this->reactionsCount($entity1, $dislikeType));
        $this->assertSame(0, $this->reactionsCount($entity2, $dislikeType));
        $this->assertSame(2, $this->reactionsCount($entity3, $dislikeType));
    }

    /** @test */
    public function it_can_recount_model_all_like_types_with_morph_map_using_full_class_name(): void
    {
        $likeType = factory(ReactionType::class)->create([
            'name' => 'Like',
            'weight' => 2,
        ]);
        $dislikeType = factory(ReactionType::class)->create([
            'name' => 'Dislike',
            'weight' => -2,
        ]);
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reacter3 = factory(Reacter::class)->create();
        $reacter4 = factory(Reacter::class)->create();
        $entity1 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $entity2 = factory(Entity::class)->create()->getLoveReactant();
        $entity3 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $reacter1->reactTo($entity1, $likeType);
        $reacter1->reactTo($entity2, $likeType);
        $reacter1->reactTo($entity3, $dislikeType);
        $reacter2->reactTo($entity1, $likeType);
        $reacter2->reactTo($entity2, $dislikeType);
        $reacter2->reactTo($entity3, $likeType);
        $reacter3->reactTo($entity1, $likeType);
        $reacter3->reactTo($entity2, $likeType);
        $reacter3->reactTo($entity3, $likeType);
        $reacter4->reactTo($entity3, $dislikeType);
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'model' => EntityWithMorphMap::class,
        ]);

        $counters = ReactionCounter::query()->count();
        $this->assertSame(0, $status);
        $this->assertSame(6, $counters);
        $this->assertSame(3, $this->reactionsCount($entity1, $likeType));
        $this->assertSame(0, $this->reactionsCount($entity2, $likeType));
        $this->assertSame(2, $this->reactionsCount($entity3, $likeType));
        $this->assertSame(0, $this->reactionsCount($entity1, $dislikeType));
        $this->assertSame(0, $this->reactionsCount($entity2, $dislikeType));
        $this->assertSame(2, $this->reactionsCount($entity3, $dislikeType));
    }

    /** @test */
    public function it_throws_model_invalid_exception_on_not_exist_morph_map(): void
    {
        $this->expectException(ReactableInvalid::class);

        $status = $this->artisan('love:recount', [
            'model' => 'not-exist-model',
        ]);

        $this->assertSame(1, $status);
    }

    /** @test */
    public function it_throws_model_invalid_exception_if_class_not_implemented_reactable_interface(): void
    {
        $this->expectException(ReactableInvalid::class);

        $status = $this->artisan('love:recount', [
            'model' => Bot::class,
        ]);

        $this->assertSame(1, $status);
    }

    /** @test */
    public function it_not_delete_reaction_counters_on_recount(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactant1 = factory(Reactant::class)->create();
        $reactant2 = factory(Reactant::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant1,
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant2,
        ]);
        $reactantCounter1 = $reactant1->reactionCounters->first();
        $reactantCounter2 = $reactant2->reactionCounters->first();
        Reaction::query()->truncate();

        $this->artisan('love:recount', [
            'type' => 'Like',
        ]);
        $this->assertTrue($reactant1->reactionCounters->first()->is($reactantCounter1));
        $this->assertTrue($reactant2->reactionCounters->first()->is($reactantCounter2));
    }

    /** @test */
    public function it_resets_reaction_counters_count_on_recount(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactant1 = factory(Reactant::class)->create();
        $reactant2 = factory(Reactant::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant1,
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant2,
        ]);
        Reaction::query()->truncate();

        $this->artisan('love:recount', [
            'type' => 'Like',
        ]);

        $this->assertSame(0, $reactant1->reactionCounters->first()->count);
        $this->assertSame(0, $reactant2->reactionCounters->first()->count);
    }

    /** @test */
    public function it_resets_reaction_counters_weight_on_recount(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'name' => 'Like',
            'weight' => 2,
        ]);
        $reactant1 = factory(Reactant::class)->create();
        $reactant2 = factory(Reactant::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant1,
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant2,
        ]);
        Reaction::query()->truncate();

        $this->artisan('love:recount', [
            'type' => 'Like',
        ]);

        $this->assertSame(0, $reactant1->reactionCounters->first()->weight);
        $this->assertSame(0, $reactant2->reactionCounters->first()->weight);
    }

    /** @test */
    public function it_not_delete_reaction_total_on_recount(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactant1 = factory(Reactant::class)->create();
        $reactant2 = factory(Reactant::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant1,
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant2,
        ]);
        $reactantTotal1 = $reactant1->reactionTotal;
        $reactantTotal2 = $reactant2->reactionTotal;
        Reaction::query()->truncate();

        $this->artisan('love:recount', [
            'type' => 'Like',
        ]);

        $this->assertTrue($reactant1->reactionTotal->is($reactantTotal1));
        $this->assertTrue($reactant2->reactionTotal->is($reactantTotal2));
    }

    /** @test */
    public function it_resets_reaction_total_count_on_recount(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $reactant1 = factory(Reactant::class)->create();
        $reactant2 = factory(Reactant::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant1,
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant2,
        ]);
        Reaction::query()->truncate();

        $this->artisan('love:recount', [
            'type' => 'Like',
        ]);

        $this->assertSame(0, $reactant1->reactionTotal->count);
        $this->assertSame(0, $reactant2->reactionTotal->count);
    }

    /** @test */
    public function it_resets_reaction_total_weight_on_recount(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'name' => 'Like',
            'weight' => 2,
        ]);
        $reactant1 = factory(Reactant::class)->create();
        $reactant2 = factory(Reactant::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant1,
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant2,
        ]);
        Reaction::query()->truncate();

        $this->artisan('love:recount', [
            'type' => 'Like',
        ]);

        $this->assertSame(0, $reactant1->reactionTotal->weight);
        $this->assertSame(0, $reactant2->reactionTotal->weight);
    }

    private function reactionsCount($reactable, ReactionTypeContract $reactionType): int
    {
        return $reactable
            ->reactionCounters()
            ->where('reaction_type_id', $reactionType->getId())
            ->first()
            ->getCount();
    }

    private function reactionsWeight($reactable, ReactionTypeContract $reactionType): int
    {
        return $reactable
            ->reactionCounters()
            ->where('reaction_type_id', $reactionType->getId())
            ->first()
            ->getWeight();
    }
}
