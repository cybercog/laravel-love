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

namespace Cog\Tests\Laravel\Love\Unit\Reacterable\Observers;

use Cog\Contracts\Love\Reacter\Models\Reacter as ReacterContract;
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
        $user = factory(User::class)->create();

        $this->assertInstanceOf(ReacterContract::class, $user->getLoveReacter());
    }

    /** @test */
    public function it_not_creates_new_reacter_on_created_if_already_exist(): void
    {
        $reacter = factory(Reacter::class)->create();
        $user = factory(User::class)->create([
            'love_reacter_id' => $reacter->getId(),
        ]);

        $this->assertSame(1, Reacter::query()->count());
        $this->assertTrue($user->getLoveReacter()->is($reacter));
    }

    /** @test */
    public function it_not_creates_new_reacter_on_created_if_opted_out(): void
    {
        $user = factory(UserWithoutAutoReacterCreate::class)->create();

        $this->assertSame(0, Reacter::query()->count());
        $this->assertInstanceOf(NullReacter::class, $user->getLoveReacter());
    }
}
