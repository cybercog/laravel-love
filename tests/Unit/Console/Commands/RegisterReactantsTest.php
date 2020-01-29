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

use Cog\Laravel\Love\Console\Commands\RegisterReactants;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\MorphMappedReactable;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;

final class RegisterReactantsTest extends TestCase
{
    /** @test */
    public function it_can_create_reactants_for_all_models_of_type(): void
    {
        Article::unsetEventDispatcher();
        User::unsetEventDispatcher();
        $articleReactables = factory(Article::class, 3)->create();
        $userReactables = factory(User::class, 3)->create();
        $reactantsCount = Reactant::query()->count();
        $command = $this->artisan(RegisterReactants::class, [
            '--model' => Article::class,
        ]);

        $status = $command->run();

        $this->assertSame(0, $status);
        $this->assertSame($reactantsCount + 3, Reactant::query()->count());
        foreach ($articleReactables as $reactable) {
            $this->assertTrue($reactable->fresh()->isRegisteredAsLoveReactant());
        }
        foreach ($userReactables as $reactable) {
            $this->assertFalse($reactable->fresh()->isRegisteredAsLoveReactant());
        }
    }

    /** @test */
    public function it_can_create_reactants_for_all_models_of_type_with_morph_map(): void
    {
        MorphMappedReactable::unsetEventDispatcher();
        User::unsetEventDispatcher();
        $morphMappedReactables = factory(MorphMappedReactable::class, 3)->create();
        $userReactables = factory(User::class, 3)->create();
        $reactantsCount = Reactant::query()->count();
        $command = $this->artisan(RegisterReactants::class, [
            '--model' => 'morph-mapped-reactable',
        ]);

        $status = $command->run();

        $this->assertSame(0, $status);
        $this->assertSame($reactantsCount + 3, Reactant::query()->count());
        foreach ($morphMappedReactables as $reactable) {
            $this->assertTrue($reactable->fresh()->isRegisteredAsLoveReactant());
        }
        foreach ($userReactables as $reactable) {
            $this->assertFalse($reactable->fresh()->isRegisteredAsLoveReactant());
        }
    }

    /** @test */
    public function it_can_create_reactants_for_specific_model_ids(): void
    {
        Article::unsetEventDispatcher();
        User::unsetEventDispatcher();
        $articleReactables = factory(Article::class, 3)->create();
        $firstArticleReactable = $articleReactables->get(0);
        $lastArticleReactable = $articleReactables->get(2);
        $userReactables = factory(User::class, 3)->create();
        $reactantsCount = Reactant::query()->count();
        $command = $this->artisan(RegisterReactants::class, [
            '--model' => Article::class,
            '--ids' => [
                $firstArticleReactable->id,
                $lastArticleReactable->id,
            ],
        ]);

        $status = $command->run();

        $this->assertSame(0, $status);
        $this->assertSame($reactantsCount + 2, Reactant::query()->count());
        $this->assertTrue($articleReactables->get(0)->fresh()->isRegisteredAsLoveReactant());
        $this->assertFalse($articleReactables->get(1)->fresh()->isRegisteredAsLoveReactant());
        $this->assertTrue($articleReactables->get(2)->fresh()->isRegisteredAsLoveReactant());
        $this->assertFalse($userReactables->get(0)->fresh()->isRegisteredAsLoveReactant());
        $this->assertFalse($userReactables->get(1)->fresh()->isRegisteredAsLoveReactant());
        $this->assertFalse($userReactables->get(2)->fresh()->isRegisteredAsLoveReactant());
    }

    /** @test */
    public function it_can_create_reactants_for_specific_model_ids_delimited_with_comma(): void
    {
        Article::unsetEventDispatcher();
        User::unsetEventDispatcher();
        $articleReactables = factory(Article::class, 3)->create();
        $firstArticleReactable = $articleReactables->get(0);
        $lastArticleReactable = $articleReactables->get(2);
        $userReactables = factory(User::class, 3)->create();
        $reactantsCount = Reactant::query()->count();
        $command = $this->artisan(RegisterReactants::class, [
            '--model' => Article::class,
            '--ids' => ["{$firstArticleReactable->id},{$lastArticleReactable->id}"],
        ]);

        $status = $command->run();

        $this->assertSame(0, $status);
        $this->assertSame($reactantsCount + 2, Reactant::query()->count());
        $this->assertTrue($articleReactables->get(0)->fresh()->isRegisteredAsLoveReactant());
        $this->assertFalse($articleReactables->get(1)->fresh()->isRegisteredAsLoveReactant());
        $this->assertTrue($articleReactables->get(2)->fresh()->isRegisteredAsLoveReactant());
        $this->assertFalse($userReactables->get(0)->fresh()->isRegisteredAsLoveReactant());
        $this->assertFalse($userReactables->get(1)->fresh()->isRegisteredAsLoveReactant());
        $this->assertFalse($userReactables->get(2)->fresh()->isRegisteredAsLoveReactant());
    }

    /** @test */
    public function it_not_create_duplicate_reactants(): void
    {
        factory(Article::class, 3)->create();
        factory(User::class, 3)->create();
        $reactantsCount = Reactant::query()->count();
        $command = $this->artisan(RegisterReactants::class, [
            '--model' => Article::class,
        ]);

        $status = $command->run();

        $this->assertSame(0, $status);
        $this->assertSame($reactantsCount, Reactant::query()->count());
    }
}
