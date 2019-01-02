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
use Cog\Contracts\Love\Reactant\ReactionCounter\Exceptions\ReactionCounterMissing;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionCounter\Models\ReactionCounter;
use Cog\Laravel\Love\Reactant\ReactionCounter\Services\ReactionCounterService;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

final class ReactionCounterServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_add_reaction(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionCounterService($reactant);
        Event::fake(); // To not fire ReactionObserver methods
        $reaction1 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);
        $reaction2 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $service->addReaction($reaction1);
        $service->addReaction($reaction2);

        $counter = $reactant->reactionCounters->first();
        $this->assertSame(2, $counter->count);
        $this->assertSame(8, $counter->weight);
    }

    /** @test */
    public function it_can_remove_reaction(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $counter = factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
            'count' => 4,
            'weight' => 16,
        ]);
        $service = new ReactionCounterService($reactant);
        Event::fake(); // To not fire ReactionObserver methods
        $reaction1 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);
        $reaction2 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $service->removeReaction($reaction1);
        $service->removeReaction($reaction2);

        $counter->refresh();
        $this->assertSame(2, $counter->count);
        $this->assertSame(8, $counter->weight);
    }

    /** @test */
    public function it_throws_exception_on_decrement_count_below_zero(): void
    {
        $this->expectException(ReactionCounterBadValue::class);

        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);
        $service = new ReactionCounterService($reactant);
        Event::fake(); // To not fire ReactionObserver methods
        $reaction1 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $service->removeReaction($reaction1);
    }

    /** @test */
    public function it_can_add_reaction_with_negative_weight(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => -4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $counter = factory(ReactionCounter::class)->create([
            'reactant_id' => $reactant->getId(),
            'reaction_type_id' => $reactionType->getId(),
        ]);
        $service = new ReactionCounterService($reactant);
        Event::fake(); // To not fire ReactionObserver methods
        $reaction1 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);
        $reaction2 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $service->addReaction($reaction1);
        $service->addReaction($reaction2);

        $counter->refresh();
        $this->assertSame(2, $counter->count);
        $this->assertSame(-8, $counter->weight);
    }

    /** @test */
    public function it_throws_exception_on_add_reaction_when_counter_not_exists(): void
    {
        $this->markTestSkipped('Do we need this?');
        Event::fake(); // Prevent counter auto creation
        $this->expectException(ReactionCounterMissing::class);

        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionCounterService($reactant);
        $reaction1 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $service->addReaction($reaction1);
    }

    /** @test */
    public function it_throws_exception_on_remove_reaction_when_counter_not_exists(): void
    {
        Event::fake(); // Prevent counter auto creation
        $this->expectException(ReactionCounterMissing::class);

        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionCounterService($reactant);
        $reaction1 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $service->removeReaction($reaction1);
    }

//    /** @test */
//    public function it_can_increment_counter(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate counter manually?');
//
//        $reactionType = factory(ReactionType::class)->create();
//        $reactant = factory(Reactant::class)->create();
//        $service = new ReactionCounterService($reactant);
//        $counter = $reactant->reactionCounters()
//            ->where('reaction_type_id', $reactionType->getId())
//            ->firstOrFail();
//
//        $service->incrementCountOfType($reactionType);
//
//        $this->assertSame(1, $counter->fresh()->count);
//    }
//
//    /** @test */
//    public function it_can_increment_counter_on_custom_amount(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate counter manually?');
//
//        $reactionType = factory(ReactionType::class)->create();
//        $reactant = factory(Reactant::class)->create();
//        $service = new ReactionCounterService($reactant);
//        $counter = $reactant->reactionCounters()
//            ->where('reaction_type_id', $reactionType->getId())
//            ->firstOrFail();
//
//        $service->incrementCountOfType($reactionType, 4);
//
//        $this->assertSame(4, $counter->fresh()->count);
//    }
//
//    /** @test */
//    public function it_can_decrement_counter(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate counter manually?');
//
//        $reactionType = factory(ReactionType::class)->create();
//        $reactant = factory(Reactant::class)->create();
//        $service = new ReactionCounterService($reactant);
//        $counter = $reactant->reactionCounters()
//            ->where('reaction_type_id', $reactionType->getId())
//            ->firstOrFail();
//        $counter->update(['count' => 4]);
//
//        $service->decrementCountOfType($reactionType);
//
//        $this->assertSame(3, $counter->fresh()->count);
//    }
//
//    /** @test */
//    public function it_can_decrement_counter_on_custom_amount(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate counter manually?');
//
//        $reactionType = factory(ReactionType::class)->create();
//        $reactant = factory(Reactant::class)->create();
//        $service = new ReactionCounterService($reactant);
//        $counter = $reactant->reactionCounters()
//            ->where('reaction_type_id', $reactionType->getId())
//            ->firstOrFail();
//        $counter->update(['count' => 4]);
//
//        $service->decrementCountOfType($reactionType, 2);
//
//        $this->assertSame(2, $counter->fresh()->count);
//    }
//
//    /** @test */
//    public function it_throws_exception_on_incrementing_not_exist_counter(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate counter manually?');
//
//        $this->expectException(ReactionCounterMissing::class);
//
//        $reactant = factory(Reactant::class)->create();
//        $service = new ReactionCounterService($reactant);
//        $reactionType = factory(ReactionType::class)->create();
//
//        $service->incrementCountOfType($reactionType);
//    }
//
//    /** @test */
//    public function it_throws_exception_on_decrementing_not_exist_counter(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate counter manually?');
//
//        $this->expectException(ReactionCounterMissing::class);
//
//        $reactant = factory(Reactant::class)->create();
//        $service = new ReactionCounterService($reactant);
//        $reactionType = factory(ReactionType::class)->create();
//
//        $service->decrementCountOfType($reactionType);
//    }
//
//    /** @test */
//    public function it_creates_counter_on_incrementing_not_exist_counter(): void
//    {
//        $this->markTestSkipped('Not sure we need this behavior.');
//
//        $reactionType = factory(ReactionType::class)->create();
//        $reactant = factory(Reactant::class)->create();
//        $service = new ReactionCounterService($reactant);
//        $oldCount = $reactant->reactionCounters()->count();
//
//        $service->incrementCountOfType($reactionType);
//
//        $assertCount = $reactant->reactionCounters()->count();
//        $this->assertSame($oldCount + 1, $assertCount);
//    }
//
//    /** @test */
//    public function it_throws_exception_on_decrement_counter_below_zero(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate counter manually?');
//
//        $this->expectException(ReactionCounterBadValue::class);
//
//        $reactionType = factory(ReactionType::class)->create();
//        $reactant = factory(Reactant::class)->create();
//        $service = new ReactionCounterService($reactant);
//
//        $service->decrementCountOfType($reactionType, 2);
//    }
//
//    /** @test */
//    public function it_can_increment_weight(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate counter manually?');
//
//        $reactionType = factory(ReactionType::class)->create();
//        $reactant = factory(Reactant::class)->create();
//        $counter = $reactant->reactionCounters()
//            ->where('reaction_type_id', $reactionType->getId())
//            ->firstOrFail();
//        $service = new ReactionCounterService($reactant);
//
//        $service->incrementWeightOfType($reactionType);
//
//        $this->assertSame(1, $counter->fresh()->weight);
//    }
//
//    /** @test */
//    public function it_can_increment_weight_on_custom_amount(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate counter manually?');
//
//        $reactionType = factory(ReactionType::class)->create();
//        $reactant = factory(Reactant::class)->create();
//        $counter = $reactant->reactionCounters()
//            ->where('reaction_type_id', $reactionType->getId())
//            ->firstOrFail();
//        $service = new ReactionCounterService($reactant);
//
//        $service->incrementWeightOfType($reactionType, 4);
//
//        $this->assertSame(4, $counter->fresh()->weight);
//    }
//
//    /** @test */
//    public function it_can_decrement_weight(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate counter manually?');
//
//        $reactionType = factory(ReactionType::class)->create();
//        $reactant = factory(Reactant::class)->create();
//        $counter = $reactant->reactionCounters()
//            ->where('reaction_type_id', $reactionType->getId())
//            ->firstOrFail();
//        $counter->update(['weight' => 4]);
//        $service = new ReactionCounterService($reactant);
//
//        $service->decrementWeightOfType($reactionType);
//
//        $this->assertSame(3, $counter->fresh()->weight);
//    }
//
//    /** @test */
//    public function it_can_decrement_weight_on_custom_amount(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate counter manually?');
//
//        $reactionType = factory(ReactionType::class)->create();
//        $reactant = factory(Reactant::class)->create();
//        $counter = $reactant->reactionCounters()
//            ->where('reaction_type_id', $reactionType->getId())
//            ->firstOrFail();
//        $counter->update(['weight' => 4]);
//        $service = new ReactionCounterService($reactant);
//
//        $service->decrementWeightOfType($reactionType, 2);
//
//        $this->assertSame(2, $counter->fresh()->weight);
//    }
//
//    /** @test */
//    public function it_throws_exception_on_incrementing_weight_of_not_exist_counter(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate counter manually?');
//
//        $this->expectException(ReactionCounterMissing::class);
//
//        $reactant = factory(Reactant::class)->create();
//        $service = new ReactionCounterService($reactant);
//        $reactionType = factory(ReactionType::class)->create();
//
//        $service->incrementWeightOfType($reactionType);
//    }
//
//    /** @test */
//    public function it_throws_exception_on_decrementing_weight_of_not_exist_counter(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate counter manually?');
//
//        $this->expectException(ReactionCounterMissing::class);
//
//        $reactant = factory(Reactant::class)->create();
//        $service = new ReactionCounterService($reactant);
//        $reactionType = factory(ReactionType::class)->create();
//
//        $service->decrementWeightOfType($reactionType);
//    }
//
//    /** @test */
//    public function it_can_decrement_weight_below_zero(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate counter manually?');
//
//        $reactionType = factory(ReactionType::class)->create();
//        $reactant = factory(Reactant::class)->create();
//        $counter = $reactant->reactionCounters()
//            ->where('reaction_type_id', $reactionType->getId())
//            ->firstOrFail();
//        $counter->update(['weight' => 1]);
//        $service = new ReactionCounterService($reactant);
//
//        $service->decrementWeightOfType($reactionType, 4);
//
//        $this->assertSame(-3, $counter->fresh()->weight);
//    }
}
