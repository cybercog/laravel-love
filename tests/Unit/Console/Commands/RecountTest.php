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
use Illuminate\Support\Str;

final class RecountTest extends TestCase
{
    private $likeType;

    private $dislikeType;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Str::startsWith($this->app->version(), '5.6')) {
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
    public function it_can_recount_reactions_of_one_reaction_type_for_any_reactable_type_when_counters_not_exists(): void
    {
        [
            $reactant1,
            $reactant2,
            $reactant3,
            $reactant4
        ] = $this->seedTestData();
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $counters = ReactionCounter::query()->count();
        $this->assertSame(4, $counters);
        $this->assertReactantLikesCount($reactant1, 3);
        $this->assertReactantLikesCount($reactant2, 2);
        $this->assertReactantLikesCount($reactant3, 2);
        $this->assertReactantLikesCount($reactant4, 1);
        $this->assertReactantLikesWeight($reactant1, 6);
        $this->assertReactantLikesWeight($reactant2, 4);
        $this->assertReactantLikesWeight($reactant3, 4);
        $this->assertReactantLikesWeight($reactant4, 2);
        $this->assertReactantDislikesCount($reactant1, 0);
        $this->assertReactantDislikesCount($reactant2, 0);
        $this->assertReactantDislikesCount($reactant3, 0);
        $this->assertReactantDislikesCount($reactant1, 0);
        $this->assertReactantDislikesWeight($reactant1, 0);
        $this->assertReactantDislikesWeight($reactant2, 0);
        $this->assertReactantDislikesWeight($reactant3, 0);
        $this->assertReactantDislikesWeight($reactant4, 0);
        $this->assertReactantTotalCount($reactant1, 3);
        $this->assertReactantTotalCount($reactant2, 2);
        $this->assertReactantTotalCount($reactant3, 2);
        $this->assertReactantTotalCount($reactant4, 1);
        $this->assertReactantTotalWeight($reactant1, 6);
        $this->assertReactantTotalWeight($reactant2, 4);
        $this->assertReactantTotalWeight($reactant3, 4);
        $this->assertReactantTotalWeight($reactant4, 2);
    }

    /** @test */
    public function it_can_recount_reactions_of_one_reaction_type_for_any_reactable_type_when_counters_exists(): void
    {
        [
            $reactant1,
            $reactant2,
            $reactant3,
            $reactant4
        ] = $this->seedTestData();

        $status = $this->artisan('love:recount', [
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $counters = ReactionCounter::query()->count();
        $this->assertSame(7, $counters);
        $this->assertReactantLikesCount($reactant1, 3);
        $this->assertReactantLikesCount($reactant2, 2);
        $this->assertReactantLikesCount($reactant3, 2);
        $this->assertReactantLikesCount($reactant4, 1);
        $this->assertReactantDislikesCount($reactant1, 0);
        $this->assertReactantDislikesCount($reactant2, 1);
        $this->assertReactantDislikesCount($reactant3, 2);
        $this->assertReactantDislikesCount($reactant4, 2);
        $this->assertReactantLikesWeight($reactant1, 6);
        $this->assertReactantLikesWeight($reactant2, 4);
        $this->assertReactantLikesWeight($reactant3, 4);
        $this->assertReactantLikesWeight($reactant4, 2);
        $this->assertReactantDislikesWeight($reactant1, 0);
        $this->assertReactantDislikesWeight($reactant2, -2);
        $this->assertReactantDislikesWeight($reactant3, -4);
        $this->assertReactantDislikesWeight($reactant4, -4);
        $this->assertReactantTotalCount($reactant1, 3);
        $this->assertReactantTotalCount($reactant2, 3);
        $this->assertReactantTotalCount($reactant3, 4);
        $this->assertReactantTotalCount($reactant4, 3);
        $this->assertReactantTotalWeight($reactant1, 6);
        $this->assertReactantTotalWeight($reactant2, 2);
        $this->assertReactantTotalWeight($reactant3, 0);
        $this->assertReactantTotalWeight($reactant4, -2);
    }

    /** @test */
    public function it_can_recount_reactions_of_one_reaction_type_for_one_reactable_type_when_counters_not_exists(): void
    {
        [
            $reactant1,
            $reactant2,
            $reactant3,
            $reactant4
        ] = $this->seedTestData();
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'reactableType' => Entity::class,
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $this->assertSame(1, ReactionCounter::query()->count());
        $this->assertReactantLikesCount($reactant1, 3);
        $this->assertReactantLikesCount($reactant2, 0);
        $this->assertReactantLikesCount($reactant3, 0);
        $this->assertReactantLikesCount($reactant4, 0);
        $this->assertReactantLikesWeight($reactant1, 6);
        $this->assertReactantLikesWeight($reactant2, 0);
        $this->assertReactantLikesWeight($reactant3, 0);
        $this->assertReactantLikesWeight($reactant4, 0);
        $this->assertReactantDislikesCount($reactant1, 0);
        $this->assertReactantDislikesCount($reactant2, 0);
        $this->assertReactantDislikesCount($reactant3, 0);
        $this->assertReactantDislikesCount($reactant4, 0);
        $this->assertReactantDislikesWeight($reactant1, 0);
        $this->assertReactantDislikesWeight($reactant2, 0);
        $this->assertReactantDislikesWeight($reactant3, 0);
        $this->assertReactantDislikesWeight($reactant4, 0);
        $this->assertReactantTotalCount($reactant1, 3);
        $this->assertReactantTotalCount($reactant2, 3);
        $this->assertReactantTotalCount($reactant3, 4);
        $this->assertReactantTotalCount($reactant4, 3);
        $this->assertReactantTotalWeight($reactant1, 6);
        $this->assertReactantTotalWeight($reactant2, 2);
        $this->assertReactantTotalWeight($reactant3, 0);
        $this->assertReactantTotalWeight($reactant4, -2);
    }

    /** @test */
    public function it_can_recount_reactions_of_one_reaction_type_for_one_reactable_type_when_counters_exists(): void
    {
        [
            $reactant1,
            $reactant2,
            $reactant3,
            $reactant4
        ] = $this->seedTestData();

        $status = $this->artisan('love:recount', [
            'reactableType' => Entity::class,
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $this->assertSame(7, ReactionCounter::query()->count());
        $this->assertReactantLikesCount($reactant1, 3);
        $this->assertReactantLikesCount($reactant2, 2);
        $this->assertReactantLikesCount($reactant3, 2);
        $this->assertReactantLikesCount($reactant4, 1);
        $this->assertReactantLikesWeight($reactant1, 6);
        $this->assertReactantLikesWeight($reactant2, 4);
        $this->assertReactantLikesWeight($reactant3, 4);
        $this->assertReactantLikesWeight($reactant4, 2);
        $this->assertReactantDislikesCount($reactant1, 0);
        $this->assertReactantDislikesCount($reactant2, 1);
        $this->assertReactantDislikesCount($reactant3, 2);
        $this->assertReactantDislikesCount($reactant4, 2);
        $this->assertReactantDislikesWeight($reactant1, 0);
        $this->assertReactantDislikesWeight($reactant2, -2);
        $this->assertReactantDislikesWeight($reactant3, -4);
        $this->assertReactantDislikesWeight($reactant4, -4);
        $this->assertReactantTotalCount($reactant1, 3);
        $this->assertReactantTotalCount($reactant2, 3);
        $this->assertReactantTotalCount($reactant3, 4);
        $this->assertReactantTotalCount($reactant4, 3);
        $this->assertReactantTotalWeight($reactant1, 6);
        $this->assertReactantTotalWeight($reactant2, 2);
        $this->assertReactantTotalWeight($reactant3, 0);
        $this->assertReactantTotalWeight($reactant4, -2);
    }

    /** @test */
    public function it_can_recount_reactions_of_one_reaction_type_for_one_reactable_morph_type_when_counters_not_exists(): void
    {
        [
            $reactant1,
            $reactant2,
            $reactant3,
            $reactant4
        ] = $this->seedTestData();
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'reactableType' => 'entity-with-morph-map',
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $this->assertSame(2, ReactionCounter::query()->count());
        $this->assertReactantLikesCount($reactant1, 0);
        $this->assertReactantLikesCount($reactant2, 2);
        $this->assertReactantLikesCount($reactant3, 0);
        $this->assertReactantLikesCount($reactant4, 1);
        $this->assertReactantLikesWeight($reactant1, 0);
        $this->assertReactantLikesWeight($reactant2, 4);
        $this->assertReactantLikesWeight($reactant3, 0);
        $this->assertReactantLikesWeight($reactant4, 2);
        $this->assertReactantDislikesCount($reactant1, 0);
        $this->assertReactantDislikesCount($reactant2, 0);
        $this->assertReactantDislikesCount($reactant3, 0);
        $this->assertReactantDislikesCount($reactant4, 0);
        $this->assertReactantDislikesWeight($reactant1, 0);
        $this->assertReactantDislikesWeight($reactant2, 0);
        $this->assertReactantDislikesWeight($reactant3, 0);
        $this->assertReactantDislikesWeight($reactant4, 0);
        $this->assertReactantTotalCount($reactant1, 3);
        $this->assertReactantTotalCount($reactant2, 2);
        $this->assertReactantTotalCount($reactant3, 4);
        $this->assertReactantTotalCount($reactant4, 1);
        $this->assertReactantTotalWeight($reactant1, 6);
        $this->assertReactantTotalWeight($reactant2, 4);
        $this->assertReactantTotalWeight($reactant3, 0);
        $this->assertReactantTotalWeight($reactant4, 2);
    }

    /** @test */
    public function it_can_recount_reactions_of_one_reaction_type_for_one_reactable_morph_type_when_counters_exists(): void
    {
        [
            $reactant1,
            $reactant2,
            $reactant3,
            $reactant4
        ] = $this->seedTestData();

        $status = $this->artisan('love:recount', [
            'reactableType' => 'entity-with-morph-map',
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $this->assertSame(7, ReactionCounter::query()->count());
        $this->assertReactantLikesCount($reactant1, 3);
        $this->assertReactantLikesCount($reactant2, 2);
        $this->assertReactantLikesCount($reactant3, 2);
        $this->assertReactantLikesCount($reactant4, 1);
        $this->assertReactantLikesWeight($reactant1, 6);
        $this->assertReactantLikesWeight($reactant2, 4);
        $this->assertReactantLikesWeight($reactant3, 4);
        $this->assertReactantLikesWeight($reactant4, 2);
        $this->assertReactantDislikesCount($reactant1, 0);
        $this->assertReactantDislikesCount($reactant2, 1);
        $this->assertReactantDislikesCount($reactant3, 2);
        $this->assertReactantDislikesCount($reactant4, 2);
        $this->assertReactantDislikesWeight($reactant1, 0);
        $this->assertReactantDislikesWeight($reactant2, -2);
        $this->assertReactantDislikesWeight($reactant3, -4);
        $this->assertReactantDislikesWeight($reactant4, -4);
        $this->assertReactantTotalCount($reactant1, 3);
        $this->assertReactantTotalCount($reactant2, 3);
        $this->assertReactantTotalCount($reactant3, 4);
        $this->assertReactantTotalCount($reactant4, 3);
        $this->assertReactantTotalWeight($reactant1, 6);
        $this->assertReactantTotalWeight($reactant2, 2);
        $this->assertReactantTotalWeight($reactant3, 0);
        $this->assertReactantTotalWeight($reactant4, -2);
    }

    /** @test */
    public function it_can_recount_reactions_of_one_reaction_type_for_one_reactable_fqcn_morph_type_when_counters_not_exists(): void
    {
        [
            $reactant1,
            $reactant2,
            $reactant3,
            $reactant4
        ] = $this->seedTestData();
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'reactableType' => EntityWithMorphMap::class,
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $this->assertSame(2, ReactionCounter::query()->count());
        $this->assertReactantLikesCount($reactant1, 0);
        $this->assertReactantLikesCount($reactant2, 2);
        $this->assertReactantLikesCount($reactant3, 0);
        $this->assertReactantLikesCount($reactant4, 1);
        $this->assertReactantLikesWeight($reactant1, 0);
        $this->assertReactantLikesWeight($reactant2, 4);
        $this->assertReactantLikesWeight($reactant3, 0);
        $this->assertReactantLikesWeight($reactant4, 2);
        $this->assertReactantDislikesCount($reactant1, 0);
        $this->assertReactantDislikesCount($reactant2, 0);
        $this->assertReactantDislikesCount($reactant3, 0);
        $this->assertReactantDislikesCount($reactant4, 0);
        $this->assertReactantDislikesWeight($reactant1, 0);
        $this->assertReactantDislikesWeight($reactant2, 0);
        $this->assertReactantDislikesWeight($reactant3, 0);
        $this->assertReactantDislikesWeight($reactant4, 0);
        $this->assertReactantTotalCount($reactant1, 3);
        $this->assertReactantTotalCount($reactant2, 2);
        $this->assertReactantTotalCount($reactant3, 4);
        $this->assertReactantTotalCount($reactant4, 1);
        $this->assertReactantTotalWeight($reactant1, 6);
        $this->assertReactantTotalWeight($reactant2, 4);
        $this->assertReactantTotalWeight($reactant3, 0);
        $this->assertReactantTotalWeight($reactant4, 2);
    }

    /** @test */
    public function it_can_recount_reactions_of_one_reaction_type_for_one_reactable_fqcn_morph_type_when_counters_exists(): void
    {
        [
            $reactant1,
            $reactant2,
            $reactant3,
            $reactant4
        ] = $this->seedTestData();

        $status = $this->artisan('love:recount', [
            'reactableType' => EntityWithMorphMap::class,
            'type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $this->assertSame(7, ReactionCounter::query()->count());
        $this->assertReactantLikesCount($reactant1, 3);
        $this->assertReactantLikesCount($reactant2, 2);
        $this->assertReactantLikesCount($reactant3, 2);
        $this->assertReactantLikesCount($reactant4, 1);
        $this->assertReactantLikesWeight($reactant1, 6);
        $this->assertReactantLikesWeight($reactant2, 4);
        $this->assertReactantLikesWeight($reactant3, 4);
        $this->assertReactantLikesWeight($reactant4, 2);
        $this->assertReactantDislikesCount($reactant1, 0);
        $this->assertReactantDislikesCount($reactant2, 1);
        $this->assertReactantDislikesCount($reactant3, 2);
        $this->assertReactantDislikesCount($reactant4, 2);
        $this->assertReactantDislikesWeight($reactant1, 0);
        $this->assertReactantDislikesWeight($reactant2, -2);
        $this->assertReactantDislikesWeight($reactant3, -4);
        $this->assertReactantDislikesWeight($reactant4, -4);
        $this->assertReactantTotalCount($reactant1, 3);
        $this->assertReactantTotalCount($reactant2, 3);
        $this->assertReactantTotalCount($reactant3, 4);
        $this->assertReactantTotalCount($reactant4, 3);
        $this->assertReactantTotalWeight($reactant1, 6);
        $this->assertReactantTotalWeight($reactant2, 2);
        $this->assertReactantTotalWeight($reactant3, 0);
        $this->assertReactantTotalWeight($reactant4, -2);
    }

    /** @test */
    public function it_can_recount_reactions_of_any_reaction_type_for_any_reactable_type_when_counters_not_exists(): void
    {
        [
            $reactant1,
            $reactant2,
            $reactant3,
            $reactant4
        ] = $this->seedTestData();
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount');

        $this->assertSame(0, $status);
        $counters = ReactionCounter::query()->count();
        $this->assertSame(7, $counters);
        $this->assertReactantLikesCount($reactant1, 3);
        $this->assertReactantLikesCount($reactant2, 2);
        $this->assertReactantLikesCount($reactant3, 2);
        $this->assertReactantLikesCount($reactant4, 1);
        $this->assertReactantLikesWeight($reactant1, 6);
        $this->assertReactantLikesWeight($reactant2, 4);
        $this->assertReactantLikesWeight($reactant3, 4);
        $this->assertReactantLikesWeight($reactant4, 2);
        $this->assertReactantDislikesCount($reactant1, 0);
        $this->assertReactantDislikesCount($reactant2, 1);
        $this->assertReactantDislikesCount($reactant3, 2);
        $this->assertReactantDislikesCount($reactant4, 2);
        $this->assertReactantDislikesWeight($reactant1, 0);
        $this->assertReactantDislikesWeight($reactant2, -2);
        $this->assertReactantDislikesWeight($reactant3, -4);
        $this->assertReactantDislikesWeight($reactant4, -4);
        $this->assertReactantTotalCount($reactant1, 3);
        $this->assertReactantTotalCount($reactant2, 3);
        $this->assertReactantTotalCount($reactant3, 4);
        $this->assertReactantTotalCount($reactant4, 3);
        $this->assertReactantTotalWeight($reactant1, 6);
        $this->assertReactantTotalWeight($reactant2, 2);
        $this->assertReactantTotalWeight($reactant3, 0);
        $this->assertReactantTotalWeight($reactant4, -2);
    }

    /** @test */
    public function it_can_recount_reactions_of_any_reaction_type_for_any_reactable_type_when_counters_exists(): void
    {
        [
            $reactant1,
            $reactant2,
            $reactant3,
            $reactant4
        ] = $this->seedTestData();

        $status = $this->artisan('love:recount');

        $this->assertSame(0, $status);
        $counters = ReactionCounter::query()->count();
        $this->assertSame(7, $counters);
        $this->assertReactantLikesCount($reactant1, 3);
        $this->assertReactantLikesCount($reactant2, 2);
        $this->assertReactantLikesCount($reactant3, 2);
        $this->assertReactantLikesCount($reactant4, 1);
        $this->assertReactantLikesWeight($reactant1, 6);
        $this->assertReactantLikesWeight($reactant2, 4);
        $this->assertReactantLikesWeight($reactant3, 4);
        $this->assertReactantLikesWeight($reactant4, 2);
        $this->assertReactantDislikesCount($reactant1, 0);
        $this->assertReactantDislikesCount($reactant2, 1);
        $this->assertReactantDislikesCount($reactant3, 2);
        $this->assertReactantDislikesCount($reactant4, 2);
        $this->assertReactantDislikesWeight($reactant1, 0);
        $this->assertReactantDislikesWeight($reactant2, -2);
        $this->assertReactantDislikesWeight($reactant3, -4);
        $this->assertReactantDislikesWeight($reactant4, -4);
        $this->assertReactantTotalCount($reactant1, 3);
        $this->assertReactantTotalCount($reactant2, 3);
        $this->assertReactantTotalCount($reactant3, 4);
        $this->assertReactantTotalCount($reactant4, 3);
        $this->assertReactantTotalWeight($reactant1, 6);
        $this->assertReactantTotalWeight($reactant2, 2);
        $this->assertReactantTotalWeight($reactant3, 0);
        $this->assertReactantTotalWeight($reactant4, -2);
    }

    /** @test */
    public function it_can_recount_reactions_of_any_reaction_type_for_one_reactable_type_when_counters_not_exists(): void
    {
        [
            $reactant1,
            $reactant2,
            $reactant3,
            $reactant4
        ] = $this->seedTestData();
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'reactableType' => Entity::class,
        ]);

        $this->assertSame(0, $status);
        $counters = ReactionCounter::query()->count();
        $this->assertSame(1, $counters);
        $this->assertReactantLikesCount($reactant1, 3);
        $this->assertReactantLikesCount($reactant2, 0);
        $this->assertReactantLikesCount($reactant3, 0);
        $this->assertReactantLikesCount($reactant4, 0);
        $this->assertReactantLikesWeight($reactant1, 6);
        $this->assertReactantLikesWeight($reactant2, 0);
        $this->assertReactantLikesWeight($reactant3, 0);
        $this->assertReactantLikesWeight($reactant4, 0);
        $this->assertReactantDislikesCount($reactant1, 0);
        $this->assertReactantDislikesCount($reactant2, 0);
        $this->assertReactantDislikesCount($reactant3, 0);
        $this->assertReactantDislikesCount($reactant4, 0);
        $this->assertReactantDislikesWeight($reactant1, 0);
        $this->assertReactantDislikesWeight($reactant2, 0);
        $this->assertReactantDislikesWeight($reactant3, 0);
        $this->assertReactantDislikesWeight($reactant4, 0);
        $this->assertReactantTotalCount($reactant1, 3);
        $this->assertReactantTotalCount($reactant2, 3);
        $this->assertReactantTotalCount($reactant3, 4);
        $this->assertReactantTotalCount($reactant4, 3);
        $this->assertReactantTotalWeight($reactant1, 6);
        $this->assertReactantTotalWeight($reactant2, 2);
        $this->assertReactantTotalWeight($reactant3, 0);
        $this->assertReactantTotalWeight($reactant4, -2);
    }

    /** @test */
    public function it_can_recount_reactions_of_any_reaction_type_for_one_reactable_type_when_counters_exists(): void
    {
        [
            $reactant1,
            $reactant2,
            $reactant3,
            $reactant4
        ] = $this->seedTestData();

        $status = $this->artisan('love:recount', [
            'reactableType' => Entity::class,
        ]);

        $this->assertSame(0, $status);
        $counters = ReactionCounter::query()->count();
        $this->assertSame(7, $counters);
        $this->assertReactantLikesCount($reactant1, 3);
        $this->assertReactantLikesCount($reactant2, 2);
        $this->assertReactantLikesCount($reactant3, 2);
        $this->assertReactantLikesCount($reactant4, 1);
        $this->assertReactantLikesWeight($reactant1, 6);
        $this->assertReactantLikesWeight($reactant2, 4);
        $this->assertReactantLikesWeight($reactant3, 4);
        $this->assertReactantLikesWeight($reactant4, 2);
        $this->assertReactantDislikesCount($reactant1, 0);
        $this->assertReactantDislikesCount($reactant2, 1);
        $this->assertReactantDislikesCount($reactant3, 2);
        $this->assertReactantDislikesCount($reactant4, 2);
        $this->assertReactantDislikesWeight($reactant1, 0);
        $this->assertReactantDislikesWeight($reactant2, -2);
        $this->assertReactantDislikesWeight($reactant3, -4);
        $this->assertReactantDislikesWeight($reactant4, -4);
        $this->assertReactantTotalCount($reactant1, 3);
        $this->assertReactantTotalCount($reactant2, 3);
        $this->assertReactantTotalCount($reactant3, 4);
        $this->assertReactantTotalCount($reactant4, 3);
        $this->assertReactantTotalWeight($reactant1, 6);
        $this->assertReactantTotalWeight($reactant2, 2);
        $this->assertReactantTotalWeight($reactant3, 0);
        $this->assertReactantTotalWeight($reactant4, -2);
    }

    /** @test */
    public function it_can_recount_reactions_of_any_reaction_type_for_one_reactable_morph_type_when_counters_not_exists(): void
    {
        [
            $reactant1,
            $reactant2,
            $reactant3,
            $reactant4
        ] = $this->seedTestData();
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'reactableType' => 'entity-with-morph-map',
        ]);

        $counters = ReactionCounter::query()->count();
        $this->assertSame(0, $status);
        $this->assertSame(4, $counters);
        $this->assertReactantLikesCount($reactant1, 0);
        $this->assertReactantLikesCount($reactant2, 2);
        $this->assertReactantLikesCount($reactant3, 0);
        $this->assertReactantLikesCount($reactant4, 1);
        $this->assertReactantLikesWeight($reactant1, 0);
        $this->assertReactantLikesWeight($reactant2, 4);
        $this->assertReactantLikesWeight($reactant3, 0);
        $this->assertReactantLikesWeight($reactant4, 2);
        $this->assertReactantDislikesCount($reactant1, 0);
        $this->assertReactantDislikesCount($reactant2, 1);
        $this->assertReactantDislikesCount($reactant3, 0);
        $this->assertReactantDislikesCount($reactant4, 2);
        $this->assertReactantDislikesWeight($reactant1, 0);
        $this->assertReactantDislikesWeight($reactant2, -2);
        $this->assertReactantDislikesWeight($reactant3, 0);
        $this->assertReactantDislikesWeight($reactant4, -4);
        $this->assertReactantTotalCount($reactant1, 3);
        $this->assertReactantTotalCount($reactant2, 3);
        $this->assertReactantTotalCount($reactant3, 4);
        $this->assertReactantTotalCount($reactant4, 3);
        $this->assertReactantTotalWeight($reactant1, 6);
        $this->assertReactantTotalWeight($reactant2, 2);
        $this->assertReactantTotalWeight($reactant3, 0);
        $this->assertReactantTotalWeight($reactant4, -2);
    }

    /** @test */
    public function it_can_recount_reactions_of_any_reaction_type_for_one_reactable_morph_type_when_counters_exists(): void
    {
        [
            $reactant1,
            $reactant2,
            $reactant3,
            $reactant4
        ] = $this->seedTestData();

        $status = $this->artisan('love:recount', [
            'reactableType' => 'entity-with-morph-map',
        ]);

        $counters = ReactionCounter::query()->count();
        $this->assertSame(0, $status);
        $this->assertSame(7, $counters);
        $this->assertReactantLikesCount($reactant1, 3);
        $this->assertReactantLikesCount($reactant2, 2);
        $this->assertReactantLikesCount($reactant3, 2);
        $this->assertReactantLikesCount($reactant4, 1);
        $this->assertReactantLikesWeight($reactant1, 6);
        $this->assertReactantLikesWeight($reactant2, 4);
        $this->assertReactantLikesWeight($reactant3, 4);
        $this->assertReactantLikesWeight($reactant4, 2);
        $this->assertReactantDislikesCount($reactant1, 0);
        $this->assertReactantDislikesCount($reactant2, 1);
        $this->assertReactantDislikesCount($reactant3, 2);
        $this->assertReactantDislikesCount($reactant4, 2);
        $this->assertReactantDislikesWeight($reactant1, 0);
        $this->assertReactantDislikesWeight($reactant2, -2);
        $this->assertReactantDislikesWeight($reactant3, -4);
        $this->assertReactantDislikesWeight($reactant4, -4);
        $this->assertReactantTotalCount($reactant1, 3);
        $this->assertReactantTotalCount($reactant2, 3);
        $this->assertReactantTotalCount($reactant3, 4);
        $this->assertReactantTotalCount($reactant4, 3);
        $this->assertReactantTotalWeight($reactant1, 6);
        $this->assertReactantTotalWeight($reactant2, 2);
        $this->assertReactantTotalWeight($reactant3, 0);
        $this->assertReactantTotalWeight($reactant4, -2);
    }

    /** @test */
    public function it_can_recount_reactions_of_any_reaction_type_for_one_reactable_fqcn_morph_type_when_counters_not_exists(): void
    {
        [
            $reactant1,
            $reactant2,
            $reactant3,
            $reactant4
        ] = $this->seedTestData();
        ReactionCounter::query()->truncate();

        $status = $this->artisan('love:recount', [
            'reactableType' => EntityWithMorphMap::class,
        ]);

        $counters = ReactionCounter::query()->count();
        $this->assertSame(0, $status);
        $this->assertSame(4, $counters);
        $this->assertReactantLikesCount($reactant1, 0);
        $this->assertReactantLikesCount($reactant2, 2);
        $this->assertReactantLikesCount($reactant3, 0);
        $this->assertReactantLikesCount($reactant4, 1);
        $this->assertReactantLikesWeight($reactant1, 0);
        $this->assertReactantLikesWeight($reactant2, 4);
        $this->assertReactantLikesWeight($reactant3, 0);
        $this->assertReactantLikesWeight($reactant4, 2);
        $this->assertReactantDislikesCount($reactant1, 0);
        $this->assertReactantDislikesCount($reactant2, 1);
        $this->assertReactantDislikesCount($reactant3, 0);
        $this->assertReactantDislikesCount($reactant4, 2);
        $this->assertReactantDislikesWeight($reactant1, 0);
        $this->assertReactantDislikesWeight($reactant2, -2);
        $this->assertReactantDislikesWeight($reactant3, 0);
        $this->assertReactantDislikesWeight($reactant4, -4);
        $this->assertReactantTotalCount($reactant1, 3);
        $this->assertReactantTotalCount($reactant2, 3);
        $this->assertReactantTotalCount($reactant3, 4);
        $this->assertReactantTotalCount($reactant4, 3);
        $this->assertReactantTotalWeight($reactant1, 6);
        $this->assertReactantTotalWeight($reactant2, 2);
        $this->assertReactantTotalWeight($reactant3, 0);
        $this->assertReactantTotalWeight($reactant4, -2);
    }

    /** @test */
    public function it_can_recount_reactions_of_any_reaction_type_for_one_reactable_fqcn_morph_type_when_counters_exists(): void
    {
        [
            $reactant1,
            $reactant2,
            $reactant3,
            $reactant4
        ] = $this->seedTestData();

        $status = $this->artisan('love:recount', [
            'reactableType' => EntityWithMorphMap::class,
        ]);

        $counters = ReactionCounter::query()->count();
        $this->assertSame(0, $status);
        $this->assertSame(7, $counters);
        $this->assertReactantLikesCount($reactant1, 3);
        $this->assertReactantLikesCount($reactant2, 2);
        $this->assertReactantLikesCount($reactant3, 2);
        $this->assertReactantLikesCount($reactant4, 1);
        $this->assertReactantLikesWeight($reactant1, 6);
        $this->assertReactantLikesWeight($reactant2, 4);
        $this->assertReactantLikesWeight($reactant3, 4);
        $this->assertReactantLikesWeight($reactant4, 2);
        $this->assertReactantDislikesCount($reactant1, 0);
        $this->assertReactantDislikesCount($reactant2, 1);
        $this->assertReactantDislikesCount($reactant3, 2);
        $this->assertReactantDislikesCount($reactant4, 2);
        $this->assertReactantDislikesWeight($reactant1, 0);
        $this->assertReactantDislikesWeight($reactant2, -2);
        $this->assertReactantDislikesWeight($reactant3, -4);
        $this->assertReactantDislikesWeight($reactant4, -4);
        $this->assertReactantTotalCount($reactant1, 3);
        $this->assertReactantTotalCount($reactant2, 3);
        $this->assertReactantTotalCount($reactant3, 4);
        $this->assertReactantTotalCount($reactant4, 3);
        $this->assertReactantTotalWeight($reactant1, 6);
        $this->assertReactantTotalWeight($reactant2, 2);
        $this->assertReactantTotalWeight($reactant3, 0);
        $this->assertReactantTotalWeight($reactant4, -2);
    }

    /** @test */
    public function it_throws_reactable_invalid_exception_on_not_exist_morph_map(): void
    {
        $this->expectException(ReactableInvalid::class);

        $status = $this->artisan('love:recount', [
            'reactableType' => 'not-exist-model',
        ]);

        $this->assertSame(1, $status);
    }

    /** @test */
    public function it_throws_reactable_invalid_exception_if_class_not_implemented_reactable_interface(): void
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

    private function reactionsCount(
        ReactantContract $reactant,
        ReactionTypeContract $reactionType
    ): int {
        return $reactant
            ->getReactionCounterOfType($reactionType)
            ->getCount();
    }

    private function reactionsWeight(
        ReactantContract $reactant,
        ReactionTypeContract $reactionType
    ): int {
        return $reactant
            ->getReactionCounterOfType($reactionType)
            ->getWeight();
    }

    private function assertReactantLikesCount(
        ReactantContract $reactant,
        int $count
    ): void {
        $this->assertSame(
            $count,
            $this->reactionsCount($reactant, $this->likeType)
        );
    }

    private function assertReactantDislikesCount(
        ReactantContract $reactant,
        int $count
    ): void {
        $this->assertSame(
            $count,
            $this->reactionsCount($reactant, $this->dislikeType)
        );
    }

    private function assertReactantLikesWeight(
        ReactantContract $reactant,
        int $count
    ): void {
        $this->assertSame(
            $count,
            $this->reactionsWeight($reactant, $this->likeType)
        );
    }

    private function assertReactantDislikesWeight(
        ReactantContract $reactant,
        int $count
    ): void {
        $this->assertSame(
            $count,
            $this->reactionsWeight($reactant, $this->dislikeType)
        );
    }

    private function assertReactantTotalCount(
        ReactantContract $reactant,
        int $count
    ): void {
        $this->assertSame(
            $count,
            $reactant->getReactionTotal()->getCount()
        );
    }

    private function assertReactantTotalWeight(
        ReactantContract $reactant,
        int $count
    ): void {
        $this->assertSame(
            $count,
            $reactant->getReactionTotal()->getWeight()
        );
    }

    private function seedTestData(): array
    {
        $reactant1 = factory(Entity::class)->create()
            ->getLoveReactant();
        $reactant2 = factory(EntityWithMorphMap::class)->create()
            ->getLoveReactant();
        $reactant3 = factory(Article::class)->create()
            ->getLoveReactant();
        $reactant4 = factory(EntityWithMorphMap::class)->create()
            ->getLoveReactant();

        $reacter1 = factory(Reacter::class)->create();
        $reacter2 = factory(Reacter::class)->create();
        $reacter3 = factory(Reacter::class)->create();
        $reacter4 = factory(Reacter::class)->create();

        $reacter1->reactTo($reactant1, $this->likeType);
        $reacter2->reactTo($reactant1, $this->likeType);
        $reacter3->reactTo($reactant1, $this->likeType);

        $reacter1->reactTo($reactant2, $this->likeType);
        $reacter3->reactTo($reactant2, $this->likeType);
        $reacter2->reactTo($reactant2, $this->dislikeType);

        $reacter2->reactTo($reactant3, $this->likeType);
        $reacter3->reactTo($reactant3, $this->likeType);
        $reacter1->reactTo($reactant3, $this->dislikeType);
        $reacter4->reactTo($reactant3, $this->dislikeType);

        $reacter1->reactTo($reactant4, $this->likeType);
        $reacter3->reactTo($reactant4, $this->dislikeType);
        $reacter4->reactTo($reactant4, $this->dislikeType);

        return [
            $reactant1, // 3 likes | 0 dislikes | Entity
            $reactant2, // 2 likes | 1 dislikes | EntityWithMorphMap
            $reactant3, // 2 likes | 2 dislikes | Article
            $reactant4, // 1 likes | 2 dislikes | EntityWithMorphMap
        ];
    }
}
