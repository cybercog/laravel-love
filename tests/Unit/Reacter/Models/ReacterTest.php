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

namespace Cog\Tests\Laravel\Love\Unit\Reacter\Models;

use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReacterTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_morph_to_reacterable(): void
    {
        $reacter = factory(Reacter::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);

        $user = factory(User::class)->create([
            'love_reacter_id' => $reacter->getKey(),
        ]);

        $this->assertTrue($reacter->reacterable->is($user));
    }
}
