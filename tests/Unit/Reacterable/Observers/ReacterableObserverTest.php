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

namespace Cog\Tests\Laravel\Love\Unit\Reacterable\Observers;

use Cog\Contracts\Love\Reacter\Models\Reacter as ReacterInterface;
use Cog\Laravel\Love\Reacter\Models\NullReacter;
use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\Stubs\Models\UserWithoutAutoReacterCreate;
use Cog\Tests\Laravel\Love\TestCase;

final class ReacterableObserverTest extends TestCase
{
    /** @test */
    public function it_creates_reacter_on_created(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(ReacterInterface::class, $user->getLoveReacter());
    }

    /** @test */
    public function it_not_creates_new_reacter_on_created_if_already_exist(): void
    {
        $reacter = Reacter::factory()->create();
        $user = User::factory()->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertSame(1, Reacter::query()->count());
        $this->assertTrue($user->getLoveReacter()->is($reacter));
    }

    /** @test */
    public function it_not_creates_new_reacter_on_created_if_opted_out(): void
    {
        $user = UserWithoutAutoReacterCreate::factory()->create();

        $this->assertSame(0, Reacter::query()->count());
        $this->assertInstanceOf(NullReacter::class, $user->getLoveReacter());
    }
}
