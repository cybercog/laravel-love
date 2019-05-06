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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

final class ReactionTypeAddTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Str::startsWith($this->app->version(), '5.6')) {
            $this->withoutMockingConsoleOutput();
        }
    }

    /** @test */
    public function it_not_creates_default_types_without_default_option(): void
    {
        $typesCount = ReactionType::query()->count();
        $status = $this->artisan('love:reaction-type-add');

        $this->assertSame(0, $status);
        $this->assertSame($typesCount, ReactionType::query()->count());
    }

    /** @test */
    public function it_creates_only_two_default_types(): void
    {
        $typesCount = ReactionType::query()->count();
        $status = $this->artisan('love:reaction-type-add --default');

        $this->assertSame(0, $status);
        $this->assertSame($typesCount + 2, ReactionType::query()->count());
    }

    /** @test */
    public function it_can_create_default_like_and_dislike_types(): void
    {
        $likeNotExistInitially = ReactionType::query()->where('name', 'Like')->doesntExist();
        $dislikeNotExistInitially = ReactionType::query()->where('name', 'Dislike')->doesntExist();
        $status = $this->artisan('love:reaction-type-add --default');
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
        $status = $this->artisan('love:reaction-type-add --default');

        $this->assertSame(0, $status);
        $this->assertSame($typesCount, ReactionType::query()->count());
    }

    /** @test */
    public function it_creates_only_missing_default_types_when_one_already_exists(): void
    {
        factory(ReactionType::class)->create([
            'name' => 'Like',
        ]);
        $typesCount = ReactionType::query()->count();
        $status = $this->artisan('love:reaction-type-add --default');

        $this->assertSame(0, $status);
        $this->assertSame($typesCount + 1, ReactionType::query()->count());
    }
}
