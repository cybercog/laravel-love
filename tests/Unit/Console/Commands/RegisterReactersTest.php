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

use Cog\Laravel\Love\Console\Commands\RegisterReacters;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Tests\Laravel\Love\Stubs\Models\Bot;
use Cog\Tests\Laravel\Love\Stubs\Models\MorphMappedReacterable;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;

final class RegisterReactersTest extends TestCase
{
    /** @test */
    public function it_can_create_reacters_for_all_models_of_type(): void
    {
        Bot::unsetEventDispatcher();
        User::unsetEventDispatcher();
        $botReactables = factory(Bot::class, 3)->create();
        $userReactables = factory(User::class, 3)->create();
        $reactersCount = Reacter::query()->count();
        $command = $this->artisan(RegisterReacters::class, [
            '--model' => Bot::class,
        ]);

        $status = $command->run();

        $this->assertSame(0, $status);
        $this->assertSame($reactersCount + 3, Reacter::query()->count());
        foreach ($botReactables as $reactable) {
            $this->assertTrue($reactable->fresh()->isRegisteredAsLoveReacter());
        }
        foreach ($userReactables as $reactable) {
            $this->assertFalse($reactable->fresh()->isRegisteredAsLoveReacter());
        }
    }

    /** @test */
    public function it_can_create_reacters_for_all_models_of_type_with_morph_map(): void
    {
        MorphMappedReacterable::unsetEventDispatcher();
        User::unsetEventDispatcher();
        $morphMappedReactables = factory(MorphMappedReacterable::class, 3)->create();
        $userReactables = factory(User::class, 3)->create();
        $reactersCount = Reacter::query()->count();
        $command = $this->artisan(RegisterReacters::class, [
            '--model' => 'morph-mapped-reacterable',
        ]);

        $status = $command->run();

        $this->assertSame(0, $status);
        $this->assertSame($reactersCount + 3, Reacter::query()->count());
        foreach ($morphMappedReactables as $reactable) {
            $this->assertTrue($reactable->fresh()->isRegisteredAsLoveReacter());
        }
        foreach ($userReactables as $reactable) {
            $this->assertFalse($reactable->fresh()->isRegisteredAsLoveReacter());
        }
    }

    /** @test */
    public function it_can_create_reacters_for_specific_model_ids(): void
    {
        Bot::unsetEventDispatcher();
        User::unsetEventDispatcher();
        $botReactables = factory(Bot::class, 3)->create();
        $firstBotReactable = $botReactables->get(0);
        $lastBotReactable = $botReactables->get(2);
        $userReactables = factory(User::class, 3)->create();
        $reactersCount = Reacter::query()->count();
        $command = $this->artisan(RegisterReacters::class, [
            '--model' => Bot::class,
            '--ids' => [
                $firstBotReactable->id,
                $lastBotReactable->id,
            ],
        ]);

        $status = $command->run();

        $this->assertSame(0, $status);
        $this->assertSame($reactersCount + 2, Reacter::query()->count());
        $this->assertTrue($botReactables->get(0)->fresh()->isRegisteredAsLoveReacter());
        $this->assertFalse($botReactables->get(1)->fresh()->isRegisteredAsLoveReacter());
        $this->assertTrue($botReactables->get(2)->fresh()->isRegisteredAsLoveReacter());
        $this->assertFalse($userReactables->get(0)->fresh()->isRegisteredAsLoveReacter());
        $this->assertFalse($userReactables->get(1)->fresh()->isRegisteredAsLoveReacter());
        $this->assertFalse($userReactables->get(2)->fresh()->isRegisteredAsLoveReacter());
    }

    /** @test */
    public function it_can_create_reacters_for_specific_model_ids_delimited_with_comma(): void
    {
        Bot::unsetEventDispatcher();
        User::unsetEventDispatcher();
        $botReactables = factory(Bot::class, 3)->create();
        $firstBotReactable = $botReactables->get(0);
        $lastBotReactable = $botReactables->get(2);
        $userReactables = factory(User::class, 3)->create();
        $reactersCount = Reacter::query()->count();
        $command = $this->artisan(RegisterReacters::class, [
            '--model' => Bot::class,
            '--ids' => ["{$firstBotReactable->id},{$lastBotReactable->id}"],
        ]);

        $status = $command->run();

        $this->assertSame(0, $status);
        $this->assertSame($reactersCount + 2, Reacter::query()->count());
        $this->assertTrue($botReactables->get(0)->fresh()->isRegisteredAsLoveReacter());
        $this->assertFalse($botReactables->get(1)->fresh()->isRegisteredAsLoveReacter());
        $this->assertTrue($botReactables->get(2)->fresh()->isRegisteredAsLoveReacter());
        $this->assertFalse($userReactables->get(0)->fresh()->isRegisteredAsLoveReacter());
        $this->assertFalse($userReactables->get(1)->fresh()->isRegisteredAsLoveReacter());
        $this->assertFalse($userReactables->get(2)->fresh()->isRegisteredAsLoveReacter());
    }

    /** @test */
    public function it_not_create_duplicate_reacters(): void
    {
        factory(Bot::class, 3)->create();
        factory(User::class, 3)->create();
        $reactersCount = Reacter::query()->count();
        $command = $this->artisan(RegisterReacters::class, [
            '--model' => Bot::class,
        ]);

        $status = $command->run();

        $this->assertSame(0, $status);
        $this->assertSame($reactersCount, Reacter::query()->count());
    }
}
