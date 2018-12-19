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

namespace Cog\Tests\Laravel\Love\Unit\Reactant\ReactionTotality\Services;

use Cog\Contracts\Love\Reactant\ReactionTotality\Exceptions\ReactionTotalityBadValue;
use Cog\Contracts\Love\Reactant\ReactionTotality\Exceptions\ReactionTotalityMissing;
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionTotality\Services\ReactionTotalityService;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

final class ReactionTotalityServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_add_reaction(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $totality = $reactant->reactionTotality;
        $service = new ReactionTotalityService($reactant);
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

        $this->assertSame(2, $totality->fresh()->count);
        $this->assertSame(8, $totality->fresh()->weight);
    }

    /** @test */
    public function it_can_remove_reaction(): void
    {
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $totality = $reactant->reactionTotality;
        $totality->update([
            'count' => 4,
            'weight' => 16,
        ]);
        $service = new ReactionTotalityService($reactant);
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

        $this->assertSame(2, $totality->fresh()->count);
        $this->assertSame(8, $totality->fresh()->weight);
    }

    /** @test */
    public function it_throws_exception_on_decrement_count_below_zero(): void
    {
        $this->expectException(ReactionTotalityBadValue::class);

        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionTotalityService($reactant);
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
        $totality = $reactant->reactionTotality;
        $service = new ReactionTotalityService($reactant);
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

        $this->assertSame(2, $totality->fresh()->count);
        $this->assertSame(-8, $totality->fresh()->weight);
    }

    /** @test */
    public function it_throws_exception_on_add_reaction_when_totality_not_exists(): void
    {
        $this->expectException(ReactionTotalityMissing::class);

        Event::fake(); // Prevent totality auto creation
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionTotalityService($reactant);
        $reaction1 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $service->addReaction($reaction1);
    }

    /** @test */
    public function it_throws_exception_on_remove_reaction_when_totality_not_exists(): void
    {
        $this->expectException(ReactionTotalityMissing::class);

        Event::fake(); // Prevent totality auto creation
        $reactionType = factory(ReactionType::class)->create([
            'weight' => 4,
        ]);
        $reactant = factory(Reactant::class)->create();
        $service = new ReactionTotalityService($reactant);
        $reaction1 = factory(Reaction::class)->create([
            'reaction_type_id' => $reactionType->getKey(),
            'reactant_id' => $reactant->getKey(),
        ]);

        $service->removeReaction($reaction1);
    }

//    /** @test */
//    public function it_can_increment_count(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate totality manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $totality = $reactant->reactionTotality;
//        $service = new ReactionTotalityService($reactant);
//
//        $service->incrementTotalCount();
//
//        $this->assertSame(1, $totality->fresh()->count);
//    }
//
//    /** @test */
//    public function it_can_increment_count_on_custom_amount(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate totality manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $totality = $reactant->reactionTotality;
//        $service = new ReactionTotalityService($reactant);
//
//        $service->incrementTotalCount(4);
//
//        $this->assertSame(4, $totality->fresh()->count);
//    }
//
//    /** @test */
//    public function it_can_decrement_count(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate totality manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $totality = $reactant->reactionTotality;
//        $totality->update([
//            'count' => 4,
//        ]);
//        $service = new ReactionTotalityService($reactant);
//
//        $service->decrementTotalCount();
//
//        $this->assertSame(3, $totality->fresh()->count);
//    }
//
//    /** @test */
//    public function it_can_decrement_count_on_custom_amount(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate totality manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $totality = $reactant->reactionTotality;
//        $totality->update([
//            'count' => 4,
//        ]);
//        $service = new ReactionTotalityService($reactant);
//
//        $service->decrementTotalCount(2);
//
//        $this->assertSame(2, $totality->fresh()->count);
//    }
//
//    /** @test */
//    public function it_throws_exception_on_incrementing_when_totality_not_exists(): void
//    {
//        Event::fake(); // Prevent totality auto creation
//        $this->expectException(ReactionTotalityMissing::class);
//
//        $reactant = factory(Reactant::class)->create();
//        $service = new ReactionTotalityService($reactant);
//
//        $service->incrementTotalCount();
//    }
//
//    /** @test */
//    public function it_throws_exception_on_decrementing_when_totality_not_exists(): void
//    {
//        Event::fake(); // Prevent totality auto creation
//        $this->expectException(ReactionTotalityMissing::class);
//
//        $reactant = factory(Reactant::class)->create();
//        $service = new ReactionTotalityService($reactant);
//
//        $service->decrementTotalCount();
//    }
//
//    /** @test */
//    public function it_creates_totality_on_instantiating_service_if_totality_not_exist(): void
//    {
//        $this->markTestSkipped('Not sure we need this behavior.');
//
//        $reactant = factory(Reactant::class)->create();
//        $oldCount = $reactant->reactionTotality()->count();
//
//        new ReactionTotalityService($reactant);
//
//        $assertCount = $reactant->reactionTotality()->count();
//        $this->assertSame($oldCount + 1, $assertCount);
//    }
//
//    /** @test */
//    public function it_throws_exception_on_decrement_count_below_zero(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate totality manually?');
//
//        $this->expectException(ReactionTotalityBadValue::class);
//
//        $reactant = factory(Reactant::class)->create();
//        factory(ReactionTotality::class)->create([
//            'reactant_id' => $reactant->getKey(),
//            'count' => 1,
//        ]);
//        $service = new ReactionTotalityService($reactant);
//
//        $service->decrementTotalCount(2);
//    }
//
//    /** @test */
//    public function it_can_increment_weight(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate totality manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $totality = $reactant->reactionTotality;
//        $service = new ReactionTotalityService($reactant);
//
//        $service->incrementTotalWeight();
//
//        $this->assertSame(1, $totality->fresh()->weight);
//    }
//
//    /** @test */
//    public function it_can_increment_weight_on_custom_amount(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate totality manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $totality = $reactant->reactionTotality;
//        $service = new ReactionTotalityService($reactant);
//
//        $service->incrementTotalWeight(4);
//
//        $this->assertSame(4, $totality->fresh()->weight);
//    }
//
//    /** @test */
//    public function it_can_decrement_weight(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate totality manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $totality = $reactant->reactionTotality;
//        $totality->update([
//            'weight' => 4,
//        ]);
//        $service = new ReactionTotalityService($reactant);
//
//        $service->decrementTotalWeight();
//
//        $this->assertSame(3, $totality->fresh()->weight);
//    }
//
//    /** @test */
//    public function it_can_decrement_weight_on_custom_amount(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate totality manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $totality = $reactant->reactionTotality;
//        $totality->update([
//            'weight' => 4,
//        ]);
//        $service = new ReactionTotalityService($reactant);
//
//        $service->decrementTotalWeight(2);
//
//        $this->assertSame(2, $totality->fresh()->weight);
//    }
//
//    /** @test */
//    public function it_can_decrement_weight_below_zero(): void
//    {
//        $this->markTestSkipped('Do we really need to manipulate totality manually?');
//
//        $reactant = factory(Reactant::class)->create();
//        $totality = $reactant->reactionTotality;
//        $totality->update([
//            'weight' => 1,
//        ]);
//        $service = new ReactionTotalityService($reactant);
//
//        $service->decrementTotalWeight(4);
//
//        $this->assertSame(-3, $totality->fresh()->weight);
//    }
}
