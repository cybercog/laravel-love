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

use Cog\Contracts\Love\Likeable\Exceptions\InvalidLikeable;
use Cog\Laravel\Love\LikeCounter\Models\LikeCounter;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\Entity;
use Cog\Tests\Laravel\Love\Stubs\Models\EntityWithMorphMap;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;

/**
 * Class Recount.
 *
 * @package Cog\Tests\Laravel\Love\Unit\Console\Commands
 */
class Recount extends TestCase
{
    /* Likes */

    /** @test */
    public function it_can_recount_all_models_likes()
    {
        if ($this->isLaravelVersion('5.7')) {
            $this->withoutMockingConsoleOutput();
        }

        $entity1 = factory(Entity::class)->create();
        $entity2 = factory(EntityWithMorphMap::class)->create();
        $article = factory(Article::class)->create();

        $entity1->dislikeBy(9);
        $entity1->likeBy(1);
        $entity1->likeBy(7);
        $entity1->likeBy(8);
        $entity2->likeBy(1);
        $entity2->likeBy(2);
        $entity2->likeBy(3);
        $entity2->likeBy(4);
        $article->likeBy(4);

        LikeCounter::truncate();

        $status = $this->artisan('love:recount', [
            'type' => 'LIKE',
        ]);

        $this->assertSame(0, $status);

        $likeCounter = LikeCounter::all();
        $this->assertCount(3, $likeCounter);
        $this->assertEquals(0, $entity1->dislikesCount);
        $this->assertEquals(3, $entity1->likesCount);
        $this->assertEquals(4, $entity2->likesCount);
        $this->assertEquals(1, $article->likesCount);
    }

    /** @test */
    public function it_can_recount_model_likes()
    {
        if ($this->isLaravelVersion('5.7')) {
            $this->withoutMockingConsoleOutput();
        }

        $entity1 = factory(Entity::class)->create();
        $entity2 = factory(Entity::class)->create();

        $entity1->dislikeBy(9);
        $entity1->likeBy(1);
        $entity1->likeBy(7);
        $entity1->likeBy(8);
        $entity2->likeBy(1);
        $entity2->likeBy(2);
        $entity2->likeBy(3);
        $entity2->likeBy(4);

        LikeCounter::truncate();

        $status = $this->artisan('love:recount', [
            'model' => Entity::class,
            'type' => 'LIKE',
        ]);

        $this->assertSame(0, $status);

        $likeCounter = LikeCounter::all();
        $this->assertCount(2, $likeCounter);
        $this->assertEquals(0, $entity1->dislikesCount);
        $this->assertEquals(3, $entity1->likesCount);
        $this->assertEquals(4, $entity2->likesCount);
    }

    /** @test */
    public function it_can_recount_model_likes_using_morph_map()
    {
        if ($this->isLaravelVersion('5.7')) {
            $this->withoutMockingConsoleOutput();
        }

        $entity1 = factory(EntityWithMorphMap::class)->create();
        $entity2 = factory(EntityWithMorphMap::class)->create();

        $entity1->dislikeBy(9);
        $entity1->likeBy(1);
        $entity1->likeBy(7);
        $entity1->likeBy(8);
        $entity2->likeBy(1);
        $entity2->likeBy(2);
        $entity2->likeBy(3);
        $entity2->likeBy(4);

        LikeCounter::truncate();

        $status = $this->artisan('love:recount', [
            'model' => 'entity-with-morph-map',
            'type' => 'LIKE',
        ]);

        $this->assertSame(0, $status);

        $likeCounter = LikeCounter::all();
        $this->assertCount(2, $likeCounter);
        $this->assertEquals(0, $entity1->dislikesCount);
        $this->assertEquals(3, $entity1->likesCount);
        $this->assertEquals(4, $entity2->likesCount);
    }

    /** @test */
    public function it_can_recount_model_likes_with_morph_map_using_full_class_name()
    {
        if ($this->isLaravelVersion('5.7')) {
            $this->withoutMockingConsoleOutput();
        }

        $entity1 = factory(EntityWithMorphMap::class)->create();
        $entity2 = factory(EntityWithMorphMap::class)->create();

        $entity1->dislikeBy(9);
        $entity1->likeBy(1);
        $entity1->likeBy(7);
        $entity1->likeBy(8);
        $entity2->likeBy(1);
        $entity2->likeBy(2);
        $entity2->likeBy(3);
        $entity2->likeBy(4);

        LikeCounter::truncate();

        $status = $this->artisan('love:recount', [
            'model' => EntityWithMorphMap::class,
            'type' => 'LIKE',
        ]);

        $this->assertSame(0, $status);

        $likeCounter = LikeCounter::all();
        $this->assertCount(2, $likeCounter);
        $this->assertEquals(0, $entity1->dislikesCount);
        $this->assertEquals(3, $entity1->likesCount);
        $this->assertEquals(4, $entity2->likesCount);
    }

    /* Dislikes */

    /** @test */
    public function it_can_recount_all_models_dislikes()
    {
        if ($this->isLaravelVersion('5.7')) {
            $this->withoutMockingConsoleOutput();
        }

        $entity1 = factory(Entity::class)->create();
        $entity2 = factory(EntityWithMorphMap::class)->create();
        $article = factory(Article::class)->create();

        $entity1->likeBy(9);
        $entity1->dislikeBy(1);
        $entity1->dislikeBy(7);
        $entity1->dislikeBy(8);
        $entity2->dislikeBy(1);
        $entity2->dislikeBy(2);
        $entity2->dislikeBy(3);
        $entity2->dislikeBy(4);
        $article->dislikeBy(4);

        LikeCounter::truncate();

        $status = $this->artisan('love:recount', [
            'type' => 'DISLIKE',
        ]);

        $this->assertSame(0, $status);

        $likeCounter = LikeCounter::all();
        $this->assertCount(3, $likeCounter);
        $this->assertEquals(0, $entity1->likesCount);
        $this->assertEquals(3, $entity1->dislikesCount);
        $this->assertEquals(4, $entity2->dislikesCount);
        $this->assertEquals(1, $article->dislikesCount);
    }

    /** @test */
    public function it_can_recount_model_dislikes()
    {
        if ($this->isLaravelVersion('5.7')) {
            $this->withoutMockingConsoleOutput();
        }

        $entity1 = factory(Entity::class)->create();
        $entity2 = factory(Entity::class)->create();

        $entity1->likeBy(9);
        $entity1->dislikeBy(1);
        $entity1->dislikeBy(7);
        $entity1->dislikeBy(8);
        $entity2->dislikeBy(1);
        $entity2->dislikeBy(2);
        $entity2->dislikeBy(3);
        $entity2->dislikeBy(4);

        LikeCounter::truncate();

        $status = $this->artisan('love:recount', [
            'model' => Entity::class,
            'type' => 'DISLIKE',
        ]);

        $this->assertSame(0, $status);

        $likeCounter = LikeCounter::all();
        $this->assertCount(2, $likeCounter);
        $this->assertEquals(0, $entity1->likesCount);
        $this->assertEquals(3, $entity1->dislikesCount);
        $this->assertEquals(4, $entity2->dislikesCount);
    }

    /** @test */
    public function it_can_recount_model_dislikes_using_morph_map()
    {
        if ($this->isLaravelVersion('5.7')) {
            $this->withoutMockingConsoleOutput();
        }

        $entity1 = factory(EntityWithMorphMap::class)->create();
        $entity2 = factory(EntityWithMorphMap::class)->create();

        $entity1->likeBy(9);
        $entity1->dislikeBy(1);
        $entity1->dislikeBy(7);
        $entity1->dislikeBy(8);
        $entity2->dislikeBy(1);
        $entity2->dislikeBy(2);
        $entity2->dislikeBy(3);
        $entity2->dislikeBy(4);

        LikeCounter::truncate();

        $status = $this->artisan('love:recount', [
            'model' => 'entity-with-morph-map',
            'type' => 'DISLIKE',
        ]);

        $this->assertSame(0, $status);

        $likeCounter = LikeCounter::all();
        $this->assertCount(2, $likeCounter);
        $this->assertEquals(0, $entity1->likesCount);
        $this->assertEquals(3, $entity1->dislikesCount);
        $this->assertEquals(4, $entity2->dislikesCount);
    }

    /** @test */
    public function it_can_recount_model_dislikes_with_morph_map_using_full_class_name()
    {
        if ($this->isLaravelVersion('5.7')) {
            $this->withoutMockingConsoleOutput();
        }

        $entity1 = factory(EntityWithMorphMap::class)->create();
        $entity2 = factory(EntityWithMorphMap::class)->create();

        $entity1->likeBy(9);
        $entity1->dislikeBy(1);
        $entity1->dislikeBy(7);
        $entity1->dislikeBy(8);
        $entity2->dislikeBy(1);
        $entity2->dislikeBy(2);
        $entity2->dislikeBy(3);
        $entity2->dislikeBy(4);

        LikeCounter::truncate();

        $status = $this->artisan('love:recount', [
            'model' => EntityWithMorphMap::class,
            'type' => 'DISLIKE',
        ]);

        $this->assertSame(0, $status);

        $likeCounter = LikeCounter::all();
        $this->assertCount(2, $likeCounter);
        $this->assertEquals(0, $entity1->likesCount);
        $this->assertEquals(3, $entity1->dislikesCount);
        $this->assertEquals(4, $entity2->dislikesCount);
    }

    /* Likes & Dislikes */

    /** @test */
    public function it_can_recount_all_models_all_like_types()
    {
        if ($this->isLaravelVersion('5.7')) {
            $this->withoutMockingConsoleOutput();
        }

        $entity1 = factory(Entity::class)->create();
        $entity2 = factory(EntityWithMorphMap::class)->create();
        $article = factory(Article::class)->create();

        $entity1->likeBy(9);
        $entity1->likeBy(7);
        $entity1->dislikeBy(8);
        $entity2->likeBy(1);
        $entity2->likeBy(2);
        $entity2->dislikeBy(3);
        $entity2->dislikeBy(4);
        $article->dislikeBy(4);

        LikeCounter::truncate();

        $status = $this->artisan('love:recount');

        $this->assertSame(0, $status);

        $likeCounter = LikeCounter::all();
        $this->assertCount(5, $likeCounter);
        $this->assertEquals(2, $entity1->likesCount);
        $this->assertEquals(1, $entity1->dislikesCount);
        $this->assertEquals(2, $entity2->likesCount);
        $this->assertEquals(2, $entity2->dislikesCount);
        $this->assertEquals(1, $article->dislikesCount);
    }

    /** @test */
    public function it_can_recount_model_all_like_types()
    {
        if ($this->isLaravelVersion('5.7')) {
            $this->withoutMockingConsoleOutput();
        }

        $entity1 = factory(Entity::class)->create();
        $entity2 = factory(Entity::class)->create();

        $entity1->likeBy(9);
        $entity1->likeBy(7);
        $entity1->dislikeBy(8);
        $entity2->likeBy(1);
        $entity2->likeBy(2);
        $entity2->dislikeBy(3);
        $entity2->dislikeBy(4);

        LikeCounter::truncate();

        $status = $this->artisan('love:recount', [
            'model' => Entity::class,
        ]);

        $this->assertSame(0, $status);

        $likeCounter = LikeCounter::all();
        $this->assertCount(4, $likeCounter);
        $this->assertEquals(2, $entity1->likesCount);
        $this->assertEquals(1, $entity1->dislikesCount);
        $this->assertEquals(2, $entity2->likesCount);
        $this->assertEquals(2, $entity2->dislikesCount);
    }

    /** @test */
    public function it_can_recount_model_all_like_types_using_morph_map()
    {
        if ($this->isLaravelVersion('5.7')) {
            $this->withoutMockingConsoleOutput();
        }

        $entity1 = factory(EntityWithMorphMap::class)->create();
        $entity2 = factory(EntityWithMorphMap::class)->create();

        $entity1->likeBy(1);
        $entity1->likeBy(7);
        $entity1->dislikeBy(8);
        $entity2->likeBy(1);
        $entity2->likeBy(2);
        $entity2->dislikeBy(3);
        $entity2->dislikeBy(4);

        LikeCounter::truncate();

        $status = $this->artisan('love:recount', [
            'model' => 'entity-with-morph-map',
        ]);

        $this->assertSame(0, $status);

        $likeCounter = LikeCounter::all();
        $this->assertCount(4, $likeCounter);
        $this->assertEquals(2, $entity1->likesCount);
        $this->assertEquals(1, $entity1->dislikesCount);
        $this->assertEquals(2, $entity2->likesCount);
        $this->assertEquals(2, $entity2->dislikesCount);
    }

    /** @test */
    public function it_can_recount_model_all_like_types_with_morph_map_using_full_class_name()
    {
        if ($this->isLaravelVersion('5.7')) {
            $this->withoutMockingConsoleOutput();
        }

        $entity1 = factory(EntityWithMorphMap::class)->create();
        $entity2 = factory(EntityWithMorphMap::class)->create();

        $entity1->likeBy(1);
        $entity1->likeBy(7);
        $entity1->dislikeBy(8);
        $entity2->likeBy(1);
        $entity2->likeBy(2);
        $entity2->dislikeBy(3);
        $entity2->dislikeBy(4);

        LikeCounter::truncate();

        $status = $this->artisan('love:recount', [
            'model' => EntityWithMorphMap::class,
        ]);

        $this->assertSame(0, $status);

        $likeCounter = LikeCounter::all();
        $this->assertCount(4, $likeCounter);
        $this->assertEquals(2, $entity1->likesCount);
        $this->assertEquals(1, $entity1->dislikesCount);
        $this->assertEquals(2, $entity2->likesCount);
        $this->assertEquals(2, $entity2->dislikesCount);
    }

    /* Exceptions */

    /** @test */
    public function it_can_throw_model_invalid_exception_on_not_exist_morph_map()
    {
        if ($this->isLaravelVersion('5.7')) {
            $this->withoutMockingConsoleOutput();
        }

        $this->expectException(InvalidLikeable::class);

        $command = $this->artisan('love:recount', [
            'model' => 'not-exist-model',
        ]);

        $this->assertSame(1, $command);
    }

    /** @test */
    public function it_can_throw_model_invalid_exception_if_class_not_implemented_has_likes_interface()
    {
        if ($this->isLaravelVersion('5.7')) {
            $this->withoutMockingConsoleOutput();
        }

        $this->expectException(InvalidLikeable::class);

        $command = $this->artisan('love:recount', [
            'model' => User::class,
        ]);

        $this->assertSame(1, $command);
    }

    public function it_deletes_records_before_recount()
    {
        // :TODO: Mock `removeLikeCountersOfType` method call
    }

    private function isLaravelVersion(string $version): bool
    {
        return starts_with($this->app->version(), $version);
    }
}
