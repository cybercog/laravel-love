<?php

/*
 * This file is part of Laravel Love.
 *
 * (c) Anton Komarev <anton@komarev.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cog\Tests\Laravel\Love\Unit\Console\Commands;

use Cog\Contracts\Love\Reactable\Exceptions\ReactableInvalid;
use Cog\Contracts\Love\Reactant\Models\Reactant as ReactantInterface;
use Cog\Contracts\Love\ReactionType\Models\ReactionType as ReactionTypeInterface;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\Bot;
use Cog\Tests\Laravel\Love\Stubs\Models\Entity;
use Cog\Tests\Laravel\Love\Stubs\Models\MorphMappedReactable;
use Cog\Tests\Laravel\Love\TestCase;

final class RecountTest extends TestCase
{
    private ReactionType $likeType;

    private ReactionType $dislikeType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMockingConsoleOutput();

        $this->likeType = ReactionType::factory()->create([
            'name' => 'Like',
            'mass' => 2,
        ]);
        $this->dislikeType = ReactionType::factory()->create([
            'name' => 'Dislike',
            'mass' => -2,
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
            '--type' => 'Like',
        ]);

        $this->assertSame(0, $status);
        $counters = ReactionCounter::query()->count();
        $this->assertSame(4, $counters);
        $this->assertReactantLikesCount($reactant1, 3);
        $this->assertReactantLikesCount($reactant2, 2);
        $this->assertReactantLikesCount($reactant3, 2);
        $this->assertReactantLikesCount($reactant4, 1);
        $this->assertReactantLikesWeight($reactant1, 6.0);
        $this->assertReactantLikesWeight($reactant2, 4.0);
        $this->assertReactantLikesWeight($reactant3, 4.0);
        $this->assertReactantLikesWeight($reactant4, 2.0);
        $this->assertReactantDislikesCount($reactant1, 0);
        $this->assertReactantDislikesCount($reactant2, 0);
        $this->assertReactantDislikesCount($reactant3, 0);
        $this->assertReactantDislikesCount($reactant1, 0);
        $this->assertReactantDislikesWeight($reactant1, 0.0);
        $this->assertReactantDislikesWeight($reactant2, 0.0);
        $this->assertReactantDislikesWeight($reactant3, 0.0);
        $this->assertReactantDislikesWeight($reactant4, 0.0);
        $this->assertReactantTotalCount($reactant1, 3);
        $this->assertReactantTotalCount($reactant2, 2);
        $this->assertReactantTotalCount($reactant3, 2);
        $this->assertReactantTotalCount($reactant4, 1);
        $this->assertReactantTotalWeight($reactant1, 6.0);
        $this->assertReactantTotalWeight($reactant2, 4.0);
        $this->assertReactantTotalWeight($reactant3, 4.0);
        $this->assertReactantTotalWeight($reactant4, 2.0);
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
            '--type' => 'Like',
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
            '--model' => Entity::class,
            '--type' => 'Like',
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
            '--model' => Entity::class,
            '--type' => 'Like',
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
            '--model' => 'morph-mapped-reactable',
            '--type' => 'Like',
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
            '--model' => 'morph-mapped-reactable',
            '--type' => 'Like',
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
            '--model' => MorphMappedReactable::class,
            '--type' => 'Like',
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
            '--model' => MorphMappedReactable::class,
            '--type' => 'Like',
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
            '--model' => Entity::class,
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
            '--model' => Entity::class,
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
            '--model' => 'morph-mapped-reactable',
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
            '--model' => 'morph-mapped-reactable',
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
            '--model' => MorphMappedReactable::class,
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
            '--model' => MorphMappedReactable::class,
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
            '--model' => 'not-exist-model',
        ]);

        $this->assertSame(1, $status);
    }

    /** @test */
    public function it_throws_reactable_invalid_exception_if_class_not_implemented_reactable_interface(): void
    {
        $this->expectException(ReactableInvalid::class);

        $status = $this->artisan('love:recount', [
            '--model' => Bot::class,
        ]);

        $this->assertSame(1, $status);
    }

    /** @test */
    public function it_not_delete_reaction_counters_on_recount(): void
    {
        $reactant1 = Reactant::factory()->create();
        $reactant2 = Reactant::factory()->create();
        Reaction::factory()->count(2)->create([
            'reaction_type_id' => $this->likeType->getKey(),
            'reactant_id' => $reactant1,
        ]);
        Reaction::factory()->count(3)->create([
            'reaction_type_id' => $this->likeType->getKey(),
            'reactant_id' => $reactant2,
        ]);
        $reactantCounter1 = $reactant1->reactionCounters->first();
        $reactantCounter2 = $reactant2->reactionCounters->first();
        Reaction::query()->truncate();

        $this->artisan('love:recount', [
            '--type' => 'Like',
        ]);
        $this->assertTrue($reactant1->reactionCounters->first()->is($reactantCounter1));
        $this->assertTrue($reactant2->reactionCounters->first()->is($reactantCounter2));
    }

    /** @test */
    public function it_resets_reaction_counters_count_on_recount(): void
    {
        $reactant1 = Reactant::factory()->create();
        $reactant2 = Reactant::factory()->create();
        Reaction::factory()->count(2)->create([
            'reaction_type_id' => $this->likeType->getKey(),
            'reactant_id' => $reactant1,
        ]);
        Reaction::factory()->count(3)->create([
            'reaction_type_id' => $this->likeType->getKey(),
            'reactant_id' => $reactant2,
        ]);
        Reaction::query()->truncate();

        $this->artisan('love:recount', [
            '--type' => 'Like',
        ]);

        $this->assertSame(0, $reactant1->reactionCounters->first()->count);
        $this->assertSame(0, $reactant2->reactionCounters->first()->count);
    }

    /** @test */
    public function it_resets_reaction_counters_weight_on_recount(): void
    {
        $reactant1 = Reactant::factory()->create();
        $reactant2 = Reactant::factory()->create();
        Reaction::factory()->count(2)->create([
            'reaction_type_id' => $this->likeType->getKey(),
            'reactant_id' => $reactant1,
        ]);
        Reaction::factory()->count(3)->create([
            'reaction_type_id' => $this->likeType->getKey(),
            'reactant_id' => $reactant2,
        ]);
        Reaction::query()->truncate();

        $this->artisan('love:recount', [
            '--type' => 'Like',
        ]);

        $this->assertSame(0.0, $reactant1->reactionCounters->first()->weight);
        $this->assertSame(0.0, $reactant2->reactionCounters->first()->weight);
    }

    /** @test */
    public function it_not_delete_reaction_total_on_recount(): void
    {
        $reactant1 = Reactant::factory()->create();
        $reactant2 = Reactant::factory()->create();
        Reaction::factory()->count(2)->create([
            'reaction_type_id' => $this->likeType->getKey(),
            'reactant_id' => $reactant1,
        ]);
        Reaction::factory()->count(3)->create([
            'reaction_type_id' => $this->likeType->getKey(),
            'reactant_id' => $reactant2,
        ]);
        $reactantTotal1 = $reactant1->reactionTotal;
        $reactantTotal2 = $reactant2->reactionTotal;
        Reaction::query()->truncate();

        $this->artisan('love:recount', [
            '--type' => 'Like',
        ]);

        $this->assertTrue($reactant1->reactionTotal->is($reactantTotal1));
        $this->assertTrue($reactant2->reactionTotal->is($reactantTotal2));
    }

    /** @test */
    public function it_resets_reaction_total_count_on_recount(): void
    {
        $reactant1 = Reactant::factory()->create();
        $reactant2 = Reactant::factory()->create();
        Reaction::factory()->count(2)->create([
            'reaction_type_id' => $this->likeType->getKey(),
            'reactant_id' => $reactant1,
        ]);
        Reaction::factory()->count(3)->create([
            'reaction_type_id' => $this->likeType->getKey(),
            'reactant_id' => $reactant2,
        ]);
        Reaction::query()->truncate();

        $this->artisan('love:recount', [
            '--type' => 'Like',
        ]);

        $this->assertSame(0, $reactant1->reactionTotal->count);
        $this->assertSame(0, $reactant2->reactionTotal->count);
    }

    /** @test */
    public function it_resets_reaction_total_weight_on_recount(): void
    {
        $reactant1 = Reactant::factory()->create();
        $reactant2 = Reactant::factory()->create();
        Reaction::factory()->count(2)->create([
            'reaction_type_id' => $this->likeType->getKey(),
            'reactant_id' => $reactant1,
        ]);
        Reaction::factory()->count(3)->create([
            'reaction_type_id' => $this->likeType->getKey(),
            'reactant_id' => $reactant2,
        ]);
        Reaction::query()->truncate();

        $this->artisan('love:recount', [
            '--type' => 'Like',
        ]);

        $this->assertSame(0.0, $reactant1->reactionTotal->weight);
        $this->assertSame(0.0, $reactant2->reactionTotal->weight);
    }

    private function reactionsCount(
        ReactantInterface $reactant,
        ReactionTypeInterface $reactionType
    ): int {
        return $reactant
            ->getReactionCounterOfType($reactionType)
            ->getCount();
    }

    private function reactionsWeight(
        ReactantInterface $reactant,
        ReactionTypeInterface $reactionType
    ): float {
        return $reactant
            ->getReactionCounterOfType($reactionType)
            ->getWeight();
    }

    private function assertReactantLikesCount(
        ReactantInterface $reactant,
        int $count
    ): void {
        $this->assertSame(
            $count,
            $this->reactionsCount($reactant, $this->likeType)
        );
    }

    private function assertReactantDislikesCount(
        ReactantInterface $reactant,
        int $count
    ): void {
        $this->assertSame(
            $count,
            $this->reactionsCount($reactant, $this->dislikeType)
        );
    }

    private function assertReactantLikesWeight(
        ReactantInterface $reactant,
        float $count
    ): void {
        $this->assertSame(
            $count,
            $this->reactionsWeight($reactant, $this->likeType)
        );
    }

    private function assertReactantDislikesWeight(
        ReactantInterface $reactant,
        float $count
    ): void {
        $this->assertSame(
            $count,
            $this->reactionsWeight($reactant, $this->dislikeType)
        );
    }

    private function assertReactantTotalCount(
        ReactantInterface $reactant,
        int $count
    ): void {
        $this->assertSame(
            $count,
            $reactant->getReactionTotal()->getCount()
        );
    }

    private function assertReactantTotalWeight(
        ReactantInterface $reactant,
        float $count
    ): void {
        $this->assertSame(
            $count,
            $reactant->getReactionTotal()->getWeight()
        );
    }

    private function seedTestData(): array
    {
        $reactant1 = Entity::factory()->create()
            ->getLoveReactant();
        $reactant2 = MorphMappedReactable::factory()->create()
            ->getLoveReactant();
        $reactant3 = Article::factory()->create()
            ->getLoveReactant();
        $reactant4 = MorphMappedReactable::factory()->create()
            ->getLoveReactant();

        $reacter1 = Reacter::factory()->create();
        $reacter2 = Reacter::factory()->create();
        $reacter3 = Reacter::factory()->create();
        $reacter4 = Reacter::factory()->create();

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
            $reactant2, // 2 likes | 1 dislikes | MorphMappedReactable
            $reactant3, // 2 likes | 2 dislikes | Article
            $reactant4, // 1 likes | 2 dislikes | MorphMappedReactable
        ];
    }
}
