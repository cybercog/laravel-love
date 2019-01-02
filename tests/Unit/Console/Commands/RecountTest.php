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
use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantContract;
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

    private $likeType;

    private $dislikeType;

    protected function setUp(): void
    {
        parent::setUp();

        if (starts_with($this->app->version(), '5.7')) {
            $this->withoutMockingConsoleOutput();
        }

        $this->likeType = factory(ReactionType::class)->create([
            'name' => 'Like',
            'weight' => 2,
        ]);
        $this->dislikeType = factory(ReactionType::class)->create([
            'name' => 'Dislike',
            'weight' => -2,
        ]);
    }

    /** @test */
    public function it_can_recount_all_models_reactions_count_only_of_type_like_when_counters_not_exist(): void
    {
        list($reactant1, $reactant2, $reactant3) = $this->seedTestData();
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $counters = ReactionCounter::query()->count();
        $this->assertSame(3, $counters);
        $this->assertSame(3, $this->reactionsCount($reactant1, $this->likeType));
        $this->assertSame(2, $this->reactionsCount($reactant2, $this->likeType));
        $this->assertSame(2, $this->reactionsCount($reactant3, $this->likeType));
        $this->assertSame(0, $this->reactionsCount($reactant1, $this->dislikeType));
        $this->assertSame(0, $this->reactionsCount($reactant2, $this->dislikeType));
        $this->assertSame(0, $this->reactionsCount($reactant3, $this->dislikeType));
    }

    /** @test */
    public function it_can_recount_all_models_reactions_count_only_of_type_like_when_counters_exist(): void
    {
        list($reactant1, $reactant2, $reactant3) = $this->seedTestData();

        $status = $this->artisan('love:recount', [
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $counters = ReactionCounter::query()->count();
        $this->assertSame(5, $counters);
        $this->assertSame(3, $this->reactionsCount($reactant1, $this->likeType));
        $this->assertSame(2, $this->reactionsCount($reactant2, $this->likeType));
        $this->assertSame(2, $this->reactionsCount($reactant3, $this->likeType));
        $this->assertSame(0, $this->reactionsCount($reactant1, $this->dislikeType));
        $this->assertSame(1, $this->reactionsCount($reactant2, $this->dislikeType));
        $this->assertSame(2, $this->reactionsCount($reactant3, $this->dislikeType));
    }

    /** @test */
    public function it_can_recount_all_models_reactions_weight_only_of_type_like_when_counters_not_exist(): void
    {
        list($reactant1, $reactant2, $reactant3) = $this->seedTestData();
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $counters = ReactionCounter::query()->count();
        $this->assertSame(3, $counters);
        $this->assertSame(6, $this->reactionsWeight($reactant1, $this->likeType));
        $this->assertSame(4, $this->reactionsWeight($reactant2, $this->likeType));
        $this->assertSame(4, $this->reactionsWeight($reactant3, $this->likeType));
        $this->assertSame(0, $this->reactionsWeight($reactant1, $this->dislikeType));
        $this->assertSame(0, $this->reactionsWeight($reactant2, $this->dislikeType));
        $this->assertSame(0, $this->reactionsWeight($reactant3, $this->dislikeType));
    }

    /** @test */
    public function it_can_recount_all_models_reactions_weight_only_of_type_like_when_counters_exist(): void
    {
        list($reactant1, $reactant2, $reactant3) = $this->seedTestData();

        $status = $this->artisan('love:recount', [
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $counters = ReactionCounter::query()->count();
        $this->assertSame(5, $counters);
        $this->assertSame(6, $this->reactionsWeight($reactant1, $this->likeType));
        $this->assertSame(4, $this->reactionsWeight($reactant2, $this->likeType));
        $this->assertSame(4, $this->reactionsWeight($reactant3, $this->likeType));
        $this->assertSame(0, $this->reactionsWeight($reactant1, $this->dislikeType));
        $this->assertSame(-2, $this->reactionsWeight($reactant2, $this->dislikeType));
        $this->assertSame(-4, $this->reactionsWeight($reactant3, $this->dislikeType));
    }

    /** @test */
    public function it_can_recount_model_likes(): void
    {
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reacter3 = factory(Reacter::class)->create();
        $reacter4 = factory(Reacter::class)->create();
        $reactant1 = factory(Entity::class)->create()->getLoveReactant();
        $reactant2 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $reactant3 = factory(Entity::class)->create()->getLoveReactant();
        $reacter1->reactTo($reactant1, $this->likeType);
        $reacter1->reactTo($reactant2, $this->likeType);
        $reacter1->reactTo($reactant3, $this->dislikeType);
        $reacter2->reactTo($reactant1, $this->likeType);
        $reacter2->reactTo($reactant2, $this->dislikeType);
        $reacter2->reactTo($reactant3, $this->likeType);
        $reacter3->reactTo($reactant1, $this->likeType);
        $reacter3->reactTo($reactant2, $this->likeType);
        $reacter3->reactTo($reactant3, $this->likeType);
        $reacter4->reactTo($reactant3, $this->dislikeType);
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'reactableType' => Entity::class,
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $this->assertSame(2, ReactionCounter::query()->count());
        $this->assertSame(3, $this->reactionsCount($reactant1, $this->likeType));
        $this->assertSame(0, $this->reactionsCount($reactant2, $this->likeType));
        $this->assertSame(2, $this->reactionsCount($reactant3, $this->likeType));
        $this->assertSame(0, $this->reactionsCount($reactant1, $this->dislikeType));
        $this->assertSame(0, $this->reactionsCount($reactant2, $this->dislikeType));
        $this->assertSame(0, $this->reactionsCount($reactant3, $this->dislikeType));
    }

    /** @test */
    public function it_can_recount_model_likes_using_morph_map(): void
    {
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reacter3 = factory(Reacter::class)->create();
        $reacter4 = factory(Reacter::class)->create();
        $reactant1 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $reactant2 = factory(Entity::class)->create()->getLoveReactant();
        $reactant3 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $reacter1->reactTo($reactant1, $this->likeType);
        $reacter1->reactTo($reactant2, $this->likeType);
        $reacter1->reactTo($reactant3, $this->dislikeType);
        $reacter2->reactTo($reactant1, $this->likeType);
        $reacter2->reactTo($reactant2, $this->dislikeType);
        $reacter2->reactTo($reactant3, $this->likeType);
        $reacter3->reactTo($reactant1, $this->likeType);
        $reacter3->reactTo($reactant2, $this->likeType);
        $reacter3->reactTo($reactant3, $this->likeType);
        $reacter4->reactTo($reactant3, $this->dislikeType);
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'reactableType' => 'entity-with-morph-map',
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $this->assertSame(2, ReactionCounter::query()->count());
        $this->assertSame(3, $this->reactionsCount($reactant1, $this->likeType));
        $this->assertSame(0, $this->reactionsCount($reactant2, $this->likeType));
        $this->assertSame(2, $this->reactionsCount($reactant3, $this->likeType));
        $this->assertSame(0, $this->reactionsCount($reactant1, $this->dislikeType));
        $this->assertSame(0, $this->reactionsCount($reactant2, $this->dislikeType));
        $this->assertSame(0, $this->reactionsCount($reactant3, $this->dislikeType));
    }

    /** @test */
    public function it_can_recount_model_likes_with_morph_map_using_full_class_name(): void
    {
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reacter3 = factory(Reacter::class)->create();
        $reacter4 = factory(Reacter::class)->create();
        $reactant1 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $reactant2 = factory(Entity::class)->create()->getLoveReactant();
        $reactant3 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $reacter1->reactTo($reactant1, $this->likeType);
        $reacter1->reactTo($reactant2, $this->likeType);
        $reacter1->reactTo($reactant3, $this->dislikeType);
        $reacter2->reactTo($reactant1, $this->likeType);
        $reacter2->reactTo($reactant2, $this->dislikeType);
        $reacter2->reactTo($reactant3, $this->likeType);
        $reacter3->reactTo($reactant1, $this->likeType);
        $reacter3->reactTo($reactant2, $this->likeType);
        $reacter3->reactTo($reactant3, $this->likeType);
        $reacter4->reactTo($reactant3, $this->dislikeType);

        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'reactableType' => EntityWithMorphMap::class,
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $this->assertSame(2, ReactionCounter::query()->count());
        $this->assertSame(3, $this->reactionsCount($reactant1, $this->likeType));
        $this->assertSame(0, $this->reactionsCount($reactant2, $this->likeType));
        $this->assertSame(2, $this->reactionsCount($reactant3, $this->likeType));
        $this->assertSame(0, $this->reactionsCount($reactant1, $this->dislikeType));
        $this->assertSame(0, $this->reactionsCount($reactant2, $this->dislikeType));
        $this->assertSame(0, $this->reactionsCount($reactant3, $this->dislikeType));
    }

    /** @test */
    public function it_can_recount_all_models_all_reaction_types(): void
    {
        list($reactant1, $reactant2, $reactant3) = $this->seedTestData();
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount');

        $this->assertSame(0, $status);
        $counters = ReactionCounter::query()->count();
        $this->assertSame(5, $counters);
        $this->assertSame(3, $this->reactionsCount($reactant1, $this->likeType));
        $this->assertSame(2, $this->reactionsCount($reactant2, $this->likeType));
        $this->assertSame(2, $this->reactionsCount($reactant3, $this->likeType));
        $this->assertSame(0, $this->reactionsCount($reactant1, $this->dislikeType));
        $this->assertSame(1, $this->reactionsCount($reactant2, $this->dislikeType));
        $this->assertSame(2, $this->reactionsCount($reactant3, $this->dislikeType));
    }

    /** @test */
    public function it_can_recount_all_models_all_reaction_types_when_counters_exists(): void
    {
        list($reactant1, $reactant2, $reactant3) = $this->seedTestData();

        $status = $this->artisan('love:recount');

        $this->assertSame(0, $status);
        $counters = ReactionCounter::query()->count();
        $this->assertSame(5, $counters);
        $this->assertSame(3, $this->reactionsCount($reactant1, $this->likeType));
        $this->assertSame(2, $this->reactionsCount($reactant2, $this->likeType));
        $this->assertSame(2, $this->reactionsCount($reactant3, $this->likeType));
        $this->assertSame(0, $this->reactionsCount($reactant1, $this->dislikeType));
        $this->assertSame(1, $this->reactionsCount($reactant2, $this->dislikeType));
        $this->assertSame(2, $this->reactionsCount($reactant3, $this->dislikeType));
    }

    /** @test */
    public function it_can_recount_model_all_reaction_types(): void
    {
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reacter3 = factory(Reacter::class)->create();
        $reacter4 = factory(Reacter::class)->create();
        $reactant1 = factory(Entity::class)->create()->getLoveReactant();
        $reactant2 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $reactant3 = factory(Entity::class)->create()->getLoveReactant();
        $reacter1->reactTo($reactant1, $this->likeType);
        $reacter1->reactTo($reactant2, $this->likeType);
        $reacter1->reactTo($reactant3, $this->dislikeType);
        $reacter2->reactTo($reactant1, $this->likeType);
        $reacter2->reactTo($reactant2, $this->dislikeType);
        $reacter2->reactTo($reactant3, $this->likeType);
        $reacter3->reactTo($reactant1, $this->likeType);
        $reacter3->reactTo($reactant2, $this->likeType);
        $reacter3->reactTo($reactant3, $this->likeType);
        $reacter4->reactTo($reactant3, $this->dislikeType);
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'reactableType' => Entity::class,
        ]);

        $this->assertSame(0, $status);
        $counters = ReactionCounter::query()->count();
        $this->assertSame(3, $counters);
        $this->assertSame(3, $this->reactionsCount($reactant1, $this->likeType));
        $this->assertSame(0, $this->reactionsCount($reactant2, $this->likeType));
        $this->assertSame(2, $this->reactionsCount($reactant3, $this->likeType));
        $this->assertSame(0, $this->reactionsCount($reactant1, $this->dislikeType));
        $this->assertSame(0, $this->reactionsCount($reactant2, $this->dislikeType));
        $this->assertSame(2, $this->reactionsCount($reactant3, $this->dislikeType));
    }

    /** @test */
    public function it_can_recount_model_all_reaction_types_using_morph_map(): void
    {
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reacter3 = factory(Reacter::class)->create();
        $reacter4 = factory(Reacter::class)->create();
        $reactant1 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $reactant2 = factory(Entity::class)->create()->getLoveReactant();
        $reactant3 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $reacter1->reactTo($reactant1, $this->likeType);
        $reacter1->reactTo($reactant2, $this->likeType);
        $reacter1->reactTo($reactant3, $this->dislikeType);
        $reacter2->reactTo($reactant1, $this->likeType);
        $reacter2->reactTo($reactant2, $this->dislikeType);
        $reacter2->reactTo($reactant3, $this->likeType);
        $reacter3->reactTo($reactant1, $this->likeType);
        $reacter3->reactTo($reactant2, $this->likeType);
        $reacter3->reactTo($reactant3, $this->likeType);
        $reacter4->reactTo($reactant3, $this->dislikeType);
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'reactableType' => 'entity-with-morph-map',
        ]);

        $counters = ReactionCounter::query()->count();
        $this->assertSame(0, $status);
        $this->assertSame(3, $counters);
        $this->assertSame(3, $this->reactionsCount($reactant1, $this->likeType));
        $this->assertSame(0, $this->reactionsCount($reactant2, $this->likeType));
        $this->assertSame(2, $this->reactionsCount($reactant3, $this->likeType));
        $this->assertSame(0, $this->reactionsCount($reactant1, $this->dislikeType));
        $this->assertSame(0, $this->reactionsCount($reactant2, $this->dislikeType));
        $this->assertSame(2, $this->reactionsCount($reactant3, $this->dislikeType));
    }

    /** @test */
    public function it_can_recount_model_all_reaction_types_with_morph_map_using_full_class_name(): void
    {
        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reacter3 = factory(Reacter::class)->create();
        $reacter4 = factory(Reacter::class)->create();
        $reactant1 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $reactant2 = factory(Entity::class)->create()->getLoveReactant();
        $reactant3 = factory(EntityWithMorphMap::class)->create()->getLoveReactant();
        $reacter1->reactTo($reactant1, $this->likeType);
        $reacter1->reactTo($reactant2, $this->likeType);
        $reacter1->reactTo($reactant3, $this->dislikeType);
        $reacter2->reactTo($reactant1, $this->likeType);
        $reacter2->reactTo($reactant2, $this->dislikeType);
        $reacter2->reactTo($reactant3, $this->likeType);
        $reacter3->reactTo($reactant1, $this->likeType);
        $reacter3->reactTo($reactant2, $this->likeType);
        $reacter3->reactTo($reactant3, $this->likeType);
        $reacter4->reactTo($reactant3, $this->dislikeType);
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'reactableType' => EntityWithMorphMap::class,
        ]);

        $counters = ReactionCounter::query()->count();
        $this->assertSame(0, $status);
        $this->assertSame(3, $counters);
        $this->assertSame(3, $this->reactionsCount($reactant1, $this->likeType));
        $this->assertSame(0, $this->reactionsCount($reactant2, $this->likeType));
        $this->assertSame(2, $this->reactionsCount($reactant3, $this->likeType));
        $this->assertSame(0, $this->reactionsCount($reactant1, $this->dislikeType));
        $this->assertSame(0, $this->reactionsCount($reactant2, $this->dislikeType));
        $this->assertSame(2, $this->reactionsCount($reactant3, $this->dislikeType));
    }

    /** @test */
    public function it_throws_model_invalid_exception_on_not_exist_morph_map(): void
    {
        $this->expectException(ReactableInvalid::class);

        $status = $this->artisan('love:recount', [
            'reactableType' => 'not-exist-model',
        ]);

        $this->assertSame(1, $status);
    }

    /** @test */
    public function it_throws_model_invalid_exception_if_class_not_implemented_reactable_interface(): void
    {
        $this->expectException(ReactableInvalid::class);

        $status = $this->artisan('love:recount', [
            'reactableType' => Bot::class,
        ]);

        $this->assertSame(1, $status);
    }

    /** @test */
    public function it_not_delete_reaction_counters_on_recount(): void
    {
        $reactant1 = factory(Reactant::class)->create();
        $reactant2 = factory(Reactant::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $this->likeType->getKey(),
            'reactant_id' => $reactant1,
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $this->likeType->getKey(),
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
        $reactant1 = factory(Reactant::class)->create();
        $reactant2 = factory(Reactant::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $this->likeType->getKey(),
            'reactant_id' => $reactant1,
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $this->likeType->getKey(),
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
        $reactant1 = factory(Reactant::class)->create();
        $reactant2 = factory(Reactant::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $this->likeType->getKey(),
            'reactant_id' => $reactant1,
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $this->likeType->getKey(),
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
        $reactant1 = factory(Reactant::class)->create();
        $reactant2 = factory(Reactant::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $this->likeType->getKey(),
            'reactant_id' => $reactant1,
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $this->likeType->getKey(),
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
        $reactant1 = factory(Reactant::class)->create();
        $reactant2 = factory(Reactant::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $this->likeType->getKey(),
            'reactant_id' => $reactant1,
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $this->likeType->getKey(),
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
        $reactant1 = factory(Reactant::class)->create();
        $reactant2 = factory(Reactant::class)->create();
        factory(Reaction::class, 2)->create([
            'reaction_type_id' => $this->likeType->getKey(),
            'reactant_id' => $reactant1,
        ]);
        factory(Reaction::class, 3)->create([
            'reaction_type_id' => $this->likeType->getKey(),
            'reactant_id' => $reactant2,
        ]);
        Reaction::query()->truncate();

        $this->artisan('love:recount', [
            'type' => 'Like',
        ]);

        $this->assertSame(0, $reactant1->reactionTotal->weight);
        $this->assertSame(0, $reactant2->reactionTotal->weight);
    }

    private function reactionsCount(ReactantContract $reactant, ReactionTypeContract $reactionType): int
    {
        return $reactant
            ->getReactionCounterOfType($reactionType)
            ->getCount();
    }

    private function reactionsWeight(ReactantContract $reactant, ReactionTypeContract $reactionType): int
    {
        return $reactant
            ->getReactionCounterOfType($reactionType)
            ->getWeight();
    }

    private function seedTestData(): array
    {
        $reactant1 = factory(Entity::class)->create()
            ->getLoveReactant();
        $reactant2 = factory(EntityWithMorphMap::class)->create()
            ->getLoveReactant();
        $reactant3 = factory(Article::class)->create()
            ->getLoveReactant();

        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reacter3 = factory(Reacter::class)->create();
        $reacter4 = factory(Reacter::class)->create();

        $reacter1->reactTo($reactant1, $this->likeType);
        $reacter1->reactTo($reactant2, $this->likeType);
        $reacter1->reactTo($reactant3, $this->dislikeType);
        $reacter2->reactTo($reactant1, $this->likeType);
        $reacter2->reactTo($reactant2, $this->dislikeType);
        $reacter2->reactTo($reactant3, $this->likeType);
        $reacter3->reactTo($reactant1, $this->likeType);
        $reacter3->reactTo($reactant2, $this->likeType);
        $reacter3->reactTo($reactant3, $this->likeType);
        $reacter4->reactTo($reactant3, $this->dislikeType);

        return [
            $reactant1,
            $reactant2,
            $reactant3,
        ];
    }
}
