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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\ReactionSummary\Services;

use Cog\Contracts\Love\Reactant\ReactionSummary\Exceptions\ReactionSummaryBadValue;
use Cog\Contracts\Love\Reactant\ReactionSummary\Exceptions\ReactionSummaryMissing;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionSummary\Services\ReactionSummaryService;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

final class ReactionSummaryServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_add_reaction(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $summary = $reactant->reactionSummary;
        $service = new ReactionSummaryService($reactant);
        Event::fake(); // To not fire ReactionObserver methods
        $reaction1 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);
        $reaction2 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $service->addReaction($reaction1);
        $service->addReaction($reaction2);

        $this->assertSame(2, $summary->fresh()->total_count);
        $this->assertSame(8, $summary->fresh()->total_weight);
    }

    /** @test */
    public function it_can_remove_reaction(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $summary = $reactant->reactionSummary;
        $summary->update([
            'total_count' => 4,
            'total_weight' => 16,
        ]);
        $service = new ReactionSummaryService($reactant);
        Event::fake(); // To not fire ReactionObserver methods
        $reaction1 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);
        $reaction2 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $service->removeReaction($reaction1);
        $service->removeReaction($reaction2);

        $this->assertSame(2, $summary->fresh()->total_count);
        $this->assertSame(8, $summary->fresh()->total_weight);
    }

    /** @test */
    public function it_throws_exception_on_decrement_total_count_below_zero(): void
    {
        $this->expectException(ReactionSummaryBadValue::class);

        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionSummaryService($reactant);
        Event::fake(); // To not fire ReactionObserver methods
        $reaction1 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
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
        $summary = $reactant->reactionSummary;
        $service = new ReactionSummaryService($reactant);
        Event::fake(); // To not fire ReactionObserver methods
        $reaction1 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);
        $reaction2 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $service->addReaction($reaction1);
        $service->addReaction($reaction2);

        $this->assertSame(2, $summary->fresh()->total_count);
        $this->assertSame(-8, $summary->fresh()->total_weight);
    }

    /** @test */
    public function it_throws_exception_on_add_reaction_when_summary_not_exists(): void
    {
        $this->expectException(ReactionSummaryMissing::class);

        Event::fake(); // Prevent summary auto creation
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionSummaryService($reactant);
        $reaction1 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $service->addReaction($reaction1);
    }

    /** @test */
    public function it_throws_exception_on_remove_reaction_when_summary_not_exists(): void
    {
        $this->expectException(ReactionSummaryMissing::class);

        Event::fake(); // Prevent summary auto creation
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionSummaryService($reactant);
        $reaction1 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $service->removeReaction($reaction1);
    }

//    /** @test */
//    public function it_can_increment_total_count(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate summary manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $summary = $reactant->reactionSummary;
//        $service = new ReactionSummaryService($reactant);
//
//        $service->incrementTotalCount();
//
//        $this->assertSame(1, $summary->fresh()->total_count);
//    }
//
//    /** @test */
//    public function it_can_increment_total_count_on_custom_amount(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate summary manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $summary = $reactant->reactionSummary;
//        $service = new ReactionSummaryService($reactant);
//
//        $service->incrementTotalCount(4);
//
//        $this->assertSame(4, $summary->fresh()->total_count);
//    }
//
//    /** @test */
//    public function it_can_decrement_total_count(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate summary manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $summary = $reactant->reactionSummary;
//        $summary->update([
//            'total_count' => 4,
//        ]);
//        $service = new ReactionSummaryService($reactant);
//
//        $service->decrementTotalCount();
//
//        $this->assertSame(3, $summary->fresh()->total_count);
//    }
//
//    /** @test */
//    public function it_can_decrement_total_count_on_custom_amount(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate summary manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $summary = $reactant->reactionSummary;
//        $summary->update([
//            'total_count' => 4,
//        ]);
//        $service = new ReactionSummaryService($reactant);
//
//        $service->decrementTotalCount(2);
//
//        $this->assertSame(2, $summary->fresh()->total_count);
//    }
//
//    /** @test */
//    public function it_throws_exception_on_incrementing_when_summary_not_exists(): void
//    {
//        Event::fake(); // Prevent summary auto creation
//        $this->expectException(ReactionSummaryMissing::class);
//
//        $reactant = factory(Reactant::class)->create();
//        $service = new ReactionSummaryService($reactant);
//
//        $service->incrementTotalCount();
//    }
//
//    /** @test */
//    public function it_throws_exception_on_decrementing_when_summary_not_exists(): void
//    {
//        Event::fake(); // Prevent summary auto creation
//        $this->expectException(ReactionSummaryMissing::class);
//
//        $reactant = factory(Reactant::class)->create();
//        $service = new ReactionSummaryService($reactant);
//
//        $service->decrementTotalCount();
//    }
//
//    /** @test */
//    public function it_creates_summary_on_instantiating_service_if_summary_not_exist(): void
//    {
//        $this->markTestSkipped('Not sure we need this behavior.');
//
//        $reactant = factory(Reactant::class)->create();
//        $oldCount = $reactant->reactionSummary()->count();
//
//        new ReactionSummaryService($reactant);
//
//        $assertCount = $reactant->reactionSummary()->count();
//        $this->assertSame($oldCount + 1, $assertCount);
//    }
//
//    /** @test */
//    public function it_throws_exception_on_decrement_total_count_below_zero(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate summary manually?');
//
//        $this->expectException(ReactionSummaryBadValue::class);
//
//        $reactant = factory(Reactant::class)->create();
//        factory(ReactionSummary::class)->create([
//            'reactant_id' => $reactant->getKey(),
//            'total_count' => 1,
//        ]);
//        $service = new ReactionSummaryService($reactant);
//
//        $service->decrementTotalCount(2);
//    }
//
//    /** @test */
//    public function it_can_increment_total_weight(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate summary manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $summary = $reactant->reactionSummary;
//        $service = new ReactionSummaryService($reactant);
//
//        $service->incrementTotalWeight();
//
//        $this->assertSame(1, $summary->fresh()->total_weight);
//    }
//
//    /** @test */
//    public function it_can_increment_total_weight_on_custom_amount(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate summary manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $summary = $reactant->reactionSummary;
//        $service = new ReactionSummaryService($reactant);
//
//        $service->incrementTotalWeight(4);
//
//        $this->assertSame(4, $summary->fresh()->total_weight);
//    }
//
//    /** @test */
//    public function it_can_decrement_total_weight(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate summary manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $summary = $reactant->reactionSummary;
//        $summary->update([
//            'total_weight' => 4,
//        ]);
//        $service = new ReactionSummaryService($reactant);
//
//        $service->decrementTotalWeight();
//
//        $this->assertSame(3, $summary->fresh()->total_weight);
//    }
//
//    /** @test */
//    public function it_can_decrement_total_weight_on_custom_amount(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate summary manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $summary = $reactant->reactionSummary;
//        $summary->update([
//            'total_weight' => 4,
//        ]);
//        $service = new ReactionSummaryService($reactant);
//
//        $service->decrementTotalWeight(2);
//
//        $this->assertSame(2, $summary->fresh()->total_weight);
//    }
//
//    /** @test */
//    public function it_can_decrement_total_weight_below_zero(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate summary manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $summary = $reactant->reactionSummary;
//        $summary->update([
//            'total_weight' => 1,
//        ]);
//        $service = new ReactionSummaryService($reactant);
//
//        $service->decrementTotalWeight(4);
//
//        $this->assertSame(-3, $summary->fresh()->total_weight);
//    }
}
