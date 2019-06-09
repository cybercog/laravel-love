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

namespace Cog\Tests\Laravel\Love\Unit\Console\Commands;

use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Support\Str;

final class ReactionTypeAddTest extends TestCase
{
    /** @test */
    public function it_creates_only_two_default_types(): void
    {
        $this->disableMocking();
        $typesCount = ReactionType::query()->count();
        $status = $this->artisan('love:reaction-type-add', ['--default' => true]);

        $this->assertSame(0, $status);
        $this->assertSame($typesCount + 2, ReactionType::query()->count());
    }

    /** @test */
    public function it_can_create_default_like_and_dislike_types(): void
    {
        $this->disableMocking();
        $likeNotExistInitially = ReactionType::query()->where('name', 'Like')->doesntExist();
        $dislikeNotExistInitially = ReactionType::query()->where('name', 'Dislike')->doesntExist();
        $status = $this->artisan('love:reaction-type-add', ['--default' => true]);
        $likeExists = ReactionType::query()->where('name', 'Like')->exists();
        $dislikeExists = ReactionType::query()->where('name', 'Dislike')->exists();

        $this->assertSame(0, $status);
        $this->assertTrue($likeNotExistInitially);
        $this->assertTrue($dislikeNotExistInitially);
        $this->assertTrue($likeExists);
        $this->assertTrue($dislikeExists);
    }

    /** @test */
    public function it_not_creates_default_like_and_dislike_types_when_already_exists(): void
    {
        factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        factory(ReactionType::class)->create([
            'name' => 'Dislike',
        ]);
        $typesCount = ReactionType::query()->count();
        $this
            ->artisan('love:reaction-type-add', ['--default' => true])
            ->expectsOutput('Reaction type with name `Like` already exists.')
            ->expectsOutput('Reaction type with name `Dislike` already exists.')
            ->assertExitCode(0);
        $this->assertSame($typesCount, ReactionType::query()->count());
    }

    /** @test */
    public function it_creates_only_missing_default_types_when_one_already_exists(): void
    {
        factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $typesCount = ReactionType::query()->count();
        $this
            ->artisan('love:reaction-type-add', ['--default' => true])
            ->expectsOutput('Reaction type with name `Like` already exists.')
            ->assertExitCode(0);
        $this->assertSame($typesCount + 1, ReactionType::query()->count());
    }

    /** @test */
    public function it_can_create_type_with_name_argument(): void
    {
        $this->disableMocking();
        $typesCount = ReactionType::query()->count();
        $status = $this->artisan('love:reaction-type-add', [
            'name' => 'TestName',
            'weight' => 4,
        ]);

        $this->assertSame(0, $status);
        $this->assertSame($typesCount + 1, ReactionType::query()->count());
        $reactionType = ReactionType::query()->latest()->first();
        $this->assertSame('TestName', $reactionType->getName());
    }

    /** @test */
    public function it_convert_type_name_to_studly_case(): void
    {
        $this->disableMocking();
        $typesCount = ReactionType::query()->count();
        $status = $this->artisan('love:reaction-type-add', [
            'name' => 'test-name',
            'weight' => 4,
        ]);

        $this->assertSame(0, $status);
        $this->assertSame($typesCount + 1, ReactionType::query()->count());
        $reactionType = ReactionType::query()->latest()->first();
        $this->assertSame('TestName', $reactionType->getName());
    }

    /** @test */
    public function it_cannot_create_type_when_name_exists(): void
    {
        factory(ReactionType::class)->create([
            'name' => 'TestName',
        ]);
        $typesCount = ReactionType::query()->count();
        $this
            ->artisan('love:reaction-type-add', ['name' => 'TestName'])
            ->expectsOutput('Reaction type with name `TestName` already exists.')
            ->assertExitCode(1);

        $this->assertSame($typesCount, ReactionType::query()->count());
    }

    /** @test */
    public function it_cannot_create_type_when_name_exists_in_other_text_case(): void
    {
        factory(ReactionType::class)->create([
            'name' => 'TestName',
        ]);
        $typesCount = ReactionType::query()->count();
        $this
            ->artisan('love:reaction-type-add', ['name' => 'test-name'])
            ->expectsOutput('Reaction type with name `TestName` already exists.')
            ->assertExitCode(1);

        $this->assertSame($typesCount, ReactionType::query()->count());
    }

    /** @test */
    public function it_can_create_type_with_weight_argument(): void
    {
        $this->disableMocking();
        $typesCount = ReactionType::query()->count();
        $status = $this->artisan('love:reaction-type-add', [
            'name' => 'TestName',
            'weight' => -4,
        ]);

        $this->assertSame(0, $status);
        $this->assertSame($typesCount + 1, ReactionType::query()->count());
        $reactionType = ReactionType::query()->latest()->first();
        $this->assertSame(-4, $reactionType->getWeight());
    }

    /** @test */
    public function it_not_creates_default_types_without_default_option(): void
    {
        $this->disableMocking();
        $typesCount = ReactionType::query()->count();
        $status = $this->artisan('love:reaction-type-add', [
            'name' => 'TestName',
            'weight' => 4,
        ]);

        $this->assertSame(0, $status);
        $this->assertSame($typesCount + 1, ReactionType::query()->count());
        $this->assertFalse(ReactionType::query()->where('name', 'Like')->exists());
        $this->assertFalse(ReactionType::query()->where('name', 'Dislike')->exists());
    }

    /** @test */
    public function it_has_valid_output_after_default_types_add(): void
    {
        $this
            ->artisan('love:reaction-type-add', ['--default' => true])
            ->expectsOutput('Reaction type with name `Like` and weight `1` was added.')
            ->expectsOutput('Reaction type with name `Dislike` and weight `-1` was added.')
            ->assertExitCode(0);
    }

    /** @test */
    public function it_asks_for_name_if_name_argument_not_exists(): void
    {
        $typesCount = ReactionType::query()->count();
        $this
            ->artisan('love:reaction-type-add', ['weight' => '4'])
            ->expectsQuestion('How to name reaction type?', 'TestName')
            ->assertExitCode(0);

        $this->assertSame($typesCount + 1, ReactionType::query()->count());
        $reactionType = ReactionType::query()->latest()->first();
        $this->assertSame('TestName', $reactionType->getName());
    }

    /** @test */
    public function it_repeat_ask_for_name_if_name_question_not_answered(): void
    {
        $typesCount = ReactionType::query()->count();
        $this
            ->artisan('love:reaction-type-add', ['weight' => '4'])
            ->expectsQuestion('How to name reaction type?', null)
            ->expectsQuestion('How to name reaction type?', 'TestName')
            ->assertExitCode(0);

        $this->assertSame($typesCount + 1, ReactionType::query()->count());
        $reactionType = ReactionType::query()->latest()->first();
        $this->assertSame('TestName', $reactionType->getName());
    }

    /** @test */
    public function it_throws_error_if_name_question_answered_with_whitespace(): void
    {
        $typesCount = ReactionType::query()->count();
        $this
            ->artisan('love:reaction-type-add', ['weight' => '4'])
            ->expectsQuestion('How to name reaction type?', '  ')
            ->expectsOutput('Reaction type with name `` is invalid.')
            ->assertExitCode(1);

        $this->assertSame($typesCount, ReactionType::query()->count());
    }

    /** @test */
    public function it_asks_for_weight_if_weight_argument_not_exists(): void
    {
        $typesCount = ReactionType::query()->count();
        $this
            ->artisan('love:reaction-type-add', ['name' => 'TestName'])
            ->expectsQuestion('What is the weight of this reaction type?', '4')
            ->assertExitCode(0);

        $this->assertSame($typesCount + 1, ReactionType::query()->count());
        $reactionType = ReactionType::query()->latest()->first();
        $this->assertSame(4, $reactionType->getWeight());
    }

    /** @test */
    public function it_creates_type_with_zero_weight_if_not_answered(): void
    {
        $typesCount = ReactionType::query()->count();
        $this
            ->artisan('love:reaction-type-add', ['name' => 'TestName'])
            ->expectsQuestion('What is the weight of this reaction type?', null)
            ->assertExitCode(0);

        $this->assertSame($typesCount + 1, ReactionType::query()->count());
        $reactionType = ReactionType::query()->latest()->first();
        $this->assertSame(0, $reactionType->getWeight());
    }

    /** @test */
    public function it_has_valid_output_after_type_add(): void
    {
        $this
            ->artisan('love:reaction-type-add', ['name' => 'TestName'])
            ->expectsQuestion('What is the weight of this reaction type?', 4)
            ->expectsOutput('Reaction type with name `TestName` and weight `4` was added.')
            ->assertExitCode(0);
    }

    private function disableMocking(): void
    {
        if (!Str::startsWith($this->app->version(), '5.6')) {
            $this->withoutMockingConsoleOutput();
        }
    }
}
