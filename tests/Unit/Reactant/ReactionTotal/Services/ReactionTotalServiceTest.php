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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\ReactionTotal\Services;

use Cog\Contracts\Love\Reactant\ReactionTotal\Exceptions\ReactionTotalBadValue;
use Cog\Contracts\Love\Reactant\ReactionTotal\Exceptions\ReactionTotalMissing;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionTotal\Services\ReactionTotalService;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

final class ReactionTotalServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_add_reaction(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $total = $reactant->reactionTotal;
        $service = new ReactionTotalService($reactant);
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

        $this->assertSame(2, $total->fresh()->count);
        $this->assertSame(8, $total->fresh()->weight);
    }

    /** @test */
    public function it_can_remove_reaction(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $total = $reactant->reactionTotal;
        $total->update([
            'count' => 4,
            'weight' => 16,
        ]);
        $service = new ReactionTotalService($reactant);
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

        $this->assertSame(2, $total->fresh()->count);
        $this->assertSame(8, $total->fresh()->weight);
    }

    /** @test */
    public function it_throws_exception_on_decrement_count_below_zero(): void
    {
        $this->expectException(ReactionTotalBadValue::class);

        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionTotalService($reactant);
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
        $total = $reactant->reactionTotal;
        $service = new ReactionTotalService($reactant);
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

        $this->assertSame(2, $total->fresh()->count);
        $this->assertSame(-8, $total->fresh()->weight);
    }

    /** @test */
    public function it_throws_exception_on_add_reaction_when_total_not_exists(): void
    {
        $this->expectException(ReactionTotalMissing::class);

        Event::fake(); // Prevent total auto creation
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionTotalService($reactant);
        $reaction1 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $service->addReaction($reaction1);
    }

    /** @test */
    public function it_throws_exception_on_remove_reaction_when_total_not_exists(): void
    {
        $this->expectException(ReactionTotalMissing::class);

        Event::fake(); // Prevent total auto creation
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionTotalService($reactant);
        $reaction1 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getId(),
            'reactant_id' => $reactant->getId(),
        ]);

        $service->removeReaction($reaction1);
    }

//    /** @test */
//    public function it_can_increment_count(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate total manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $total = $reactant->reactionTotal;
//        $service = new ReactionTotalService($reactant);
//
//        $service->incrementTotalCount();
//
//        $this->assertSame(1, $total->fresh()->count);
//    }
//
//    /** @test */
//    public function it_can_increment_count_on_custom_amount(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate total manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $total = $reactant->reactionTotal;
//        $service = new ReactionTotalService($reactant);
//
//        $service->incrementTotalCount(4);
//
//        $this->assertSame(4, $total->fresh()->count);
//    }
//
//    /** @test */
//    public function it_can_decrement_count(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate total manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $total = $reactant->reactionTotal;
//        $total->update([
//            'count' => 4,
//        ]);
//        $service = new ReactionTotalService($reactant);
//
//        $service->decrementTotalCount();
//
//        $this->assertSame(3, $total->fresh()->count);
//    }
//
//    /** @test */
//    public function it_can_decrement_count_on_custom_amount(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate total manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $total = $reactant->reactionTotal;
//        $total->update([
//            'count' => 4,
//        ]);
//        $service = new ReactionTotalService($reactant);
//
//        $service->decrementTotalCount(2);
//
//        $this->assertSame(2, $total->fresh()->count);
//    }
//
//    /** @test */
//    public function it_throws_exception_on_incrementing_when_total_not_exists(): void
//    {
//        Event::fake(); // Prevent total auto creation
//        $this->expectException(ReactionTotalMissing::class);
//
//        $reactant = factory(Reactant::class)->create();
//        $service = new ReactionTotalService($reactant);
//
//        $service->incrementTotalCount();
//    }
//
//    /** @test */
//    public function it_throws_exception_on_decrementing_when_total_not_exists(): void
//    {
//        Event::fake(); // Prevent total auto creation
//        $this->expectException(ReactionTotalMissing::class);
//
//        $reactant = factory(Reactant::class)->create();
//        $service = new ReactionTotalService($reactant);
//
//        $service->decrementTotalCount();
//    }
//
//    /** @test */
//    public function it_creates_total_on_instantiating_service_if_total_not_exist(): void
//    {
//        $this->markTestSkipped('Not sure we need this behavior.');
//
//        $reactant = factory(Reactant::class)->create();
//        $oldCount = $reactant->reactionTotal()->count();
//
//        new ReactionTotalService($reactant);
//
//        $assertCount = $reactant->reactionTotal()->count();
//        $this->assertSame($oldCount + 1, $assertCount);
//    }
//
//    /** @test */
//    public function it_throws_exception_on_decrement_count_below_zero(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate total manually?');
//
//        $this->expectException(ReactionTotalBadValue::class);
//
//        $reactant = factory(Reactant::class)->create();
//        factory(ReactionTotal::class)->create([
//            'reactant_id' => $reactant->getId(),
//            'count' => 1,
//        ]);
//        $service = new ReactionTotalService($reactant);
//
//        $service->decrementTotalCount(2);
//    }
//
//    /** @test */
//    public function it_can_increment_weight(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate total manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $total = $reactant->reactionTotal;
//        $service = new ReactionTotalService($reactant);
//
//        $service->incrementTotalWeight();
//
//        $this->assertSame(1, $total->fresh()->weight);
//    }
//
//    /** @test */
//    public function it_can_increment_weight_on_custom_amount(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate total manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $total = $reactant->reactionTotal;
//        $service = new ReactionTotalService($reactant);
//
//        $service->incrementTotalWeight(4);
//
//        $this->assertSame(4, $total->fresh()->weight);
//    }
//
//    /** @test */
//    public function it_can_decrement_weight(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate total manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $total = $reactant->reactionTotal;
//        $total->update([
//            'weight' => 4,
//        ]);
//        $service = new ReactionTotalService($reactant);
//
//        $service->decrementTotalWeight();
//
//        $this->assertSame(3, $total->fresh()->weight);
//    }
//
//    /** @test */
//    public function it_can_decrement_weight_on_custom_amount(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate total manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $total = $reactant->reactionTotal;
//        $total->update([
//            'weight' => 4,
//        ]);
//        $service = new ReactionTotalService($reactant);
//
//        $service->decrementTotalWeight(2);
//
//        $this->assertSame(2, $total->fresh()->weight);
//    }
//
//    /** @test */
//    public function it_can_decrement_weight_below_zero(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate total manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $total = $reactant->reactionTotal;
//        $total->update([
//            'weight' => 1,
//        ]);
//        $service = new ReactionTotalService($reactant);
//
//        $service->decrementTotalWeight(4);
//
//        $this->assertSame(-3, $total->fresh()->weight);
//    }
}
