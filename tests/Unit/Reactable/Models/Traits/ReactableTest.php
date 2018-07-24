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

namespace Cog\Tests\Laravel\Love\Unit\Reactable\Models\Traits;

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReactableTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_belong_to_reacter(): void
    {
        $reactant = factory(Reactant::class)->create([
            'type' => (new User())->getMorphClass(),
        ]);

        $reactable = factory(Article::class)->create([
            'love_reactant_id' => $reactant->getKey(),
        ]);

        $this->assertTrue($reactable->reactant->is($reactant));
    }
}
