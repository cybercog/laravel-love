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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\ReactionCounter\Services;

use Cog\Contracts\Love\Reactant\ReactionCounter\Exceptions\ReactionCounterBadValue;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionCounter\Services\ReactionCounterService;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReactionCounterServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_increment_counter(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionCounterService($reactant);
        $counter = factory(ReactionCounter::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
            'count' => 0,
        ]);

        $service->incrementCounterOfType($reactionType);

        $this->assertSame(1, $counter->fresh()->count);
    }

    /** @test */
    public function it_can_increment_counter_on_custom_amount(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionCounterService($reactant);
        $counter = factory(ReactionCounter::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
            'count' => 0,
        ]);

        $service->incrementCounterOfType($reactionType, 4);

        $this->assertSame(4, $counter->fresh()->count);
    }

    /** @test */
    public function it_can_decrement_counter(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionCounterService($reactant);
        $counter = factory(ReactionCounter::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
            'count' => 4,
        ]);

        $service->decrementCounterOfType($reactionType);

        $this->assertSame(3, $counter->fresh()->count);
    }

    /** @test */
    public function it_can_decrement_counter_on_custom_amount(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionCounterService($reactant);
        $counter = factory(ReactionCounter::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
            'count' => 4,
        ]);

        $service->decrementCounterOfType($reactionType, 2);

        $this->assertSame(2, $counter->fresh()->count);
    }

    /** @test */
    public function it_throws_exception_on_incrementing_not_exist_counter(): void
    {
        $this->markTestSkipped('Not sure we need so strict behavior.');

        $this->expectException(\RuntimeException::class);

        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionCounterService($reactant);

        $service->incrementCounterOfType($reactionType);
    }

    /** @test */
    public function it_throws_exception_on_decrementing_not_exist_counter(): void
    {
        $this->markTestSkipped('Not sure we need so strict behavior.');

        $this->expectException(\RuntimeException::class);

        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionCounterService($reactant);

        $service->decrementCounterOfType($reactionType);
    }

    /** @test */
    public function it_creates_counter_on_incrementing_not_exist_counter(): void
    {
        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionCounterService($reactant);
        $oldCount = $reactant->reactionCounters()->count();

        $service->incrementCounterOfType($reactionType);

        $assertCount = $reactant->reactionCounters()->count();
        $this->assertSame($oldCount + 1, $assertCount);
    }

    /** @test */
    public function it_throws_exception_on_decrement_counter_below_zero(): void
    {
        $this->expectException(ReactionCounterBadValue::class);

        $reactionType = factory(ReactionType::class)->create();
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionCounterService($reactant);
        factory(ReactionCounter::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
            'count' => 1,
        ]);

        $service->decrementCounterOfType($reactionType, 2);
    }
}
