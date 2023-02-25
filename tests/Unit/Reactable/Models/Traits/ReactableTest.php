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

namespace Cog\Tests\Laravel\Love\Unit\Reactable\Models\Traits;

use Cog\Contracts\Love\Reactable\Exceptions\AlreadyRegisteredAsLoveReactant;
use Cog\Laravel\Love\Reactant\Facades\Reactant as ReactantFacade;
use Cog\Laravel\Love\Reactant\Models\NullReactant;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Support\Facades\Event;

final class ReactableTest extends TestCase
{
    /** @test */
    public function it_can_belong_to_love_reactant(): void
    {
        $reactant = Reactant::factory()->create([
            'type' => (new Article())->getMorphClass(),
        ]);

        $reactable = Article::factory()->create([
            'love_reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($reactable->loveReactant->is($reactant));
    }

    /** @test */
    public function it_can_get_love_reactant(): void
    {
        $reactant = Reactant::factory()->create([
            'type' => (new Article())->getMorphClass(),
        ]);

        $reactable = Article::factory()->create([
            'love_reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($reactable->getLoveReactant()->is($reactant));
    }

    /** @test */
    public function it_can_get_reactant_null_object_when_reactant_is_null(): void
    {
        $reactable = new Article();

        $reactant = $reactable->getLoveReactant();

        $this->assertInstanceOf(NullReactant::class, $reactant);
    }

    /** @test */
    public function it_can_get_reactant_facade(): void
    {
        $reactant = Reactant::factory()->create([
            'type' => (new Article())->getMorphClass(),
        ]);
        $reactable = Article::factory()->create([
            'love_reactant_id' => $reactant->getId(),
        ]);

        $reactantFacade = $reactable->viaLoveReactant();

        $this->assertInstanceOf(ReactantFacade::class, $reactantFacade);
    }

    /** @test */
    public function it_can_get_reactant_facade_when_reactant_is_null(): void
    {
        $reactable = new Article();

        $reactantFacade = $reactable->viaLoveReactant();

        $this->assertInstanceOf(ReactantFacade::class, $reactantFacade);
    }

    /** @test */
    public function it_register_reactable_as_reactant_on_create(): void
    {
        $reactable = new Article([
            'name' => 'Test Article',
        ]);
        $reactable->save();

        $this->assertTrue($reactable->isRegisteredAsLoveReactant());
        $this->assertInstanceOf(Reactant::class, $reactable->getLoveReactant());
    }

    /** @test */
    public function it_not_create_new_reactant_if_manually_registered_reactable_as_reactant_on_create(): void
    {
        $reactant = Reactant::factory()->create([
            'type' => (new Article())->getMorphClass(),
        ]);
        $reactable = new Article([
            'name' => 'Test Article',
        ]);
        $reactable->setAttribute('love_reactant_id', $reactant->getId());
        $reactable->save();

        $this->assertSame(1, Reactant::query()->count());
        $this->assertTrue($reactable->isRegisteredAsLoveReactant());
        $this->assertInstanceOf(Reactant::class, $reactable->getLoveReactant());
    }

    /** @test */
    public function it_can_check_if_registered_as_reactant(): void
    {
        $reactant = Reactant::factory()->create([
            'type' => (new Article())->getMorphClass(),
        ]);
        $notRegisteredReactable = new Article();
        $registeredReactable = Article::factory()->create([
            'love_reactant_id' => $reactant->getId(),
        ]);

        $this->assertTrue($registeredReactable->isRegisteredAsLoveReactant());
        $this->assertFalse($notRegisteredReactable->isRegisteredAsLoveReactant());
    }

    /** @test */
    public function it_can_check_if_not_registered_as_reactant(): void
    {
        $reactant = Reactant::factory()->create([
            'type' => (new Article())->getMorphClass(),
        ]);
        $notRegisteredReactable = new Article();
        $registeredReactable = Article::factory()->create([
            'love_reactant_id' => $reactant->getId(),
        ]);

        $this->assertFalse($registeredReactable->isNotRegisteredAsLoveReactant());
        $this->assertTrue($notRegisteredReactable->isNotRegisteredAsLoveReactant());
    }

    /** @test */
    public function it_can_register_as_love_reactant(): void
    {
        Event::fake();
        $article = Article::factory()->create();

        $article->registerAsLoveReactant();

        $this->assertInstanceOf(Reactant::class, $article->getLoveReactant());
    }

    /** @test */
    public function it_throws_exception_on_register_as_love_reactant_when_already_registered(): void
    {
        $this->expectException(AlreadyRegisteredAsLoveReactant::class);

        $article = Article::factory()->create();

        $article->registerAsLoveReactant();
    }
}
