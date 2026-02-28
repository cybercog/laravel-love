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

namespace Cog\Tests\Laravel\Love\Unit\Reacterable\Models\Traits;

use Cog\Contracts\Love\Reacterable\Exceptions\AlreadyRegisteredAsLoveReacter;
use Cog\Laravel\Love\Reacter\Facades\Reacter as ReacterFacade;
use Cog\Laravel\Love\Reacter\Models\NullReacter;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Tests\Laravel\Love\Stubs\Models\Bot;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Support\Facades\Event;

final class ReacterableTest extends TestCase
{
    public function test_can_belong_to_love_reacter(): void
    {
        $reacter = Reacter::factory()->create([
            'type' => (new User())->getMorphClass(),
        ]);

        $reacterable = User::factory()->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertTrue($reacterable->loveReacter->is($reacter));
    }

    public function test_can_get_love_reacter(): void
    {
        $reacter = Reacter::factory()->create([
            'type' => (new User())->getMorphClass(),
        ]);

        $reacterable = User::factory()->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertTrue($reacterable->getLoveReacter()->is($reacter));
    }

    public function test_can_get_reacter_null_object_when_reacter_is_null(): void
    {
        $reacterable = new User();

        $reacter = $reacterable->getLoveReacter();

        $this->assertInstanceOf(NullReacter::class, $reacter);
    }

    public function test_can_get_reacter_facade(): void
    {
        $reacter = Reacter::factory()->create([
            'type' => (new User())->getMorphClass(),
        ]);
        $reacterable = User::factory()->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $reacterFacade = $reacterable->viaLoveReacter();

        $this->assertInstanceOf(ReacterFacade::class, $reacterFacade);
    }

    public function test_can_get_reacter_facade_when_reacter_is_null(): void
    {
        $reacterable = new User();

        $reacterFacade = $reacterable->viaLoveReacter();

        $this->assertInstanceOf(ReacterFacade::class, $reacterFacade);
    }

    public function test_register_reacterable_as_reacter_on_create(): void
    {
        $reacterable = new Bot([
            'name' => 'TestBot',
        ]);
        $reacterable->save();

        $this->assertTrue($reacterable->isRegisteredAsLoveReacter());
        $this->assertInstanceOf(Reacter::class, $reacterable->getLoveReacter());
    }

    public function test_not_create_new_reacter_if_manually_registered_reacterable_as_reacter_on_create(): void
    {
        $reacter = Reacter::factory()->create([
            'type' => (new Bot())->getMorphClass(),
        ]);
        $reacterable = new Bot([
            'name' => 'TestBot',
        ]);
        $reacterable->setAttribute('love_reacter_id', $reacter->getId());
        $reacterable->save();

        $this->assertSame(1, Reacter::query()->count());
        $this->assertTrue($reacterable->isRegisteredAsLoveReacter());
        $this->assertInstanceOf(Reacter::class, $reacterable->getLoveReacter());
    }

    public function test_can_check_if_registered_as_love_reacter(): void
    {
        $reacter = Reacter::factory()->create([
            'type' => (new User())->getMorphClass(),
        ]);
        $notRegisteredReacterable = new User();
        $registeredReacterable = User::factory()->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertTrue($registeredReacterable->isRegisteredAsLoveReacter());
        $this->assertFalse($notRegisteredReacterable->isRegisteredAsLoveReacter());
    }

    public function test_can_check_if_not_registered_as_love_reacter(): void
    {
        $reacter = Reacter::factory()->create([
            'type' => (new User())->getMorphClass(),
        ]);
        $notRegisteredReacterable = new User();
        $registeredReacterable = User::factory()->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertFalse($registeredReacterable->isNotRegisteredAsLoveReacter());
        $this->assertTrue($notRegisteredReacterable->isNotRegisteredAsLoveReacter());
    }

    public function test_can_register_as_love_reacter(): void
    {
        Event::fake();
        $user = User::factory()->create();

        $user->registerAsLoveReacter();

        $this->assertInstanceOf(Reacter::class, $user->getLoveReacter());
    }

    public function test_throws_exception_on_register_as_love_reacter_when_already_registered(): void
    {
        $this->expectException(AlreadyRegisteredAsLoveReacter::class);

        $user = User::factory()->create();

        $user->registerAsLoveReacter();
    }
}
