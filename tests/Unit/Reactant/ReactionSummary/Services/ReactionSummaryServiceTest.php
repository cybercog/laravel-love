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
use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Laravel\Love\Reactant\ReactionSummary\Models\ReactionSummary;
use Cog\Laravel\Love\Reactant\ReactionSummary\Services\ReactionSummaryService;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReactionSummaryServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_increment_total_count(): void
    {
        $reactant = factory(Reactant::class)->create();
        $summary = factory(ReactionSummary::class)->create([
            'reactant_id' => $reactant->getKey(),
            'total_count' => 0,
        ]);
        $service = new ReactionSummaryService($reactant);

        $service->incrementTotalCount();

        $this->assertSame(1, $summary->fresh()->total_count);
    }

    /** @test */
    public function it_can_increment_total_count_on_custom_amount(): void
    {
        $reactant = factory(Reactant::class)->create();
        $summary = factory(ReactionSummary::class)->create([
            'reactant_id' => $reactant->getKey(),
            'total_count' => 0,
        ]);
        $service = new ReactionSummaryService($reactant);

        $service->incrementTotalCount(4);

        $this->assertSame(4, $summary->fresh()->total_count);
    }

    /** @test */
    public function it_can_decrement_total_count(): void
    {
        $reactant = factory(Reactant::class)->create();
        $summary = factory(ReactionSummary::class)->create([
            'reactant_id' => $reactant->getKey(),
            'total_count' => 4,
        ]);
        $service = new ReactionSummaryService($reactant);

        $service->decrementTotalCount();

        $this->assertSame(3, $summary->fresh()->total_count);
    }

    /** @test */
    public function it_can_decrement_total_count_on_custom_amount(): void
    {
        $reactant = factory(Reactant::class)->create();
        $summary = factory(ReactionSummary::class)->create([
            'reactant_id' => $reactant->getKey(),
            'total_count' => 4,
        ]);
        $service = new ReactionSummaryService($reactant);

        $service->decrementTotalCount(2);

        $this->assertSame(2, $summary->fresh()->total_count);
    }

    /** @test */
    public function it_throws_exception_on_incrementing_not_exist_summary(): void
    {
        $this->markTestSkipped('Not sure we need so strict behavior.');

        $this->expectException(\RuntimeException::class);

        $reactant = factory(Reactant::class)->create();
        $service = new ReactionSummaryService($reactant);

        $service->incrementTotalCount();
    }

    /** @test */
    public function it_throws_exception_on_decrementing_not_exist_summary(): void
    {
        $this->markTestSkipped('Not sure we need so strict behavior.');

        $this->expectException(\RuntimeException::class);

        $reactant = factory(Reactant::class)->create();
        $service = new ReactionSummaryService($reactant);

        $service->decrementTotalCount();
    }

    /** @test */
    public function it_creates_summary_on_instantiating_service_if_summary_not_exist(): void
    {
        $reactant = factory(Reactant::class)->create();
        $oldCount = $reactant->reactionSummary()->count();

        new ReactionSummaryService($reactant);

        $assertCount = $reactant->reactionSummary()->count();
        $this->assertSame($oldCount + 1, $assertCount);
    }

    /** @test */
    public function it_throws_exception_on_decrement_total_count_below_zero(): void
    {
        $this->expectException(ReactionSummaryBadValue::class);

        $reactant = factory(Reactant::class)->create();
        factory(ReactionSummary::class)->create([
            'reactant_id' => $reactant->getKey(),
            'total_count' => 1,
        ]);
        $service = new ReactionSummaryService($reactant);

        $service->decrementTotalCount(2);
    }

    /** @test */
    public function it_can_increment_total_weight(): void
    {
        $reactant = factory(Reactant::class)->create();
        $summary = factory(ReactionSummary::class)->create([
            'reactant_id' => $reactant->getKey(),
            'total_weight' => 0,
        ]);
        $service = new ReactionSummaryService($reactant);

        $service->incrementTotalWeight();

        $this->assertSame(1, $summary->fresh()->total_weight);
    }

    /** @test */
    public function it_can_increment_total_weight_on_custom_amount(): void
    {
        $reactant = factory(Reactant::class)->create();
        $summary = factory(ReactionSummary::class)->create([
            'reactant_id' => $reactant->getKey(),
            'total_weight' => 0,
        ]);
        $service = new ReactionSummaryService($reactant);

        $service->incrementTotalWeight(4);

        $this->assertSame(4, $summary->fresh()->total_weight);
    }

    /** @test */
    public function it_can_decrement_total_weight(): void
    {
        $reactant = factory(Reactant::class)->create();
        $summary = factory(ReactionSummary::class)->create([
            'reactant_id' => $reactant->getKey(),
            'total_weight' => 4,
        ]);
        $service = new ReactionSummaryService($reactant);

        $service->decrementTotalWeight();

        $this->assertSame(3, $summary->fresh()->total_weight);
    }

    /** @test */
    public function it_can_decrement_total_weight_on_custom_amount(): void
    {
        $reactant = factory(Reactant::class)->create();
        $summary = factory(ReactionSummary::class)->create([
            'reactant_id' => $reactant->getKey(),
            'total_weight' => 4,
        ]);
        $service = new ReactionSummaryService($reactant);

        $service->decrementTotalWeight(2);

        $this->assertSame(2, $summary->fresh()->total_weight);
    }

    /** @test */
    public function it_can_decrement_total_weight_below_zero(): void
    {
        $reactant = factory(Reactant::class)->create();
        $summary = factory(ReactionSummary::class)->create([
            'reactant_id' => $reactant->getKey(),
            'total_weight' => 1,
        ]);
        $service = new ReactionSummaryService($reactant);

        $service->decrementTotalWeight(4);

        $this->assertSame(-3, $summary->fresh()->total_weight);
    }
}
