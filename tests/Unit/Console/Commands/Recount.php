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

use Cog\Laravel\Love\LikeCounter\Models\LikeCounter;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Cog\Tests\Laravel\Love\Stubs\Models\Entity;
use Cog\Tests\Laravel\Love\Stubs\Models\EntityWithMorphMap;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Contracts\Console\Kernel;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class Recount.
 *
 * @package Cog\Tests\Laravel\Love\Unit\Console\Commands
 */
class Recount extends TestCase
{
    protected $kernel;

    protected function setUp()
    {
        parent::setUp();

        $this->kernel = $this->app->make(Kernel::class);
    }

    /* Likes */

    /** @test */
    public function it_can_recount_all_models_likes()
    {
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

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'love:recount',
                'type' => 'LIKE',
            ]),
            $output = new BufferedOutput
        );

        $this->assertEquals(0, $status);

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

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'love:recount',
                'model' => Entity::class,
                'type' => 'LIKE',
            ]),
            $output = new BufferedOutput
        );

        $this->assertEquals(0, $status);

        $likeCounter = LikeCounter::all();
        $this->assertCount(2, $likeCounter);
        $this->assertEquals(0, $entity1->dislikesCount);
        $this->assertEquals(3, $entity1->likesCount);
        $this->assertEquals(4, $entity2->likesCount);
    }

    /** @test */
    public function it_can_recount_model_likes_using_morph_map()
    {
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

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'love:recount',
                'model' => 'entity-with-morph-map',
                'type' => 'LIKE',
            ]),
            $output = new BufferedOutput
        );

        $this->assertEquals(0, $status);

        $likeCounter = LikeCounter::all();
        $this->assertCount(2, $likeCounter);
        $this->assertEquals(0, $entity1->dislikesCount);
        $this->assertEquals(3, $entity1->likesCount);
        $this->assertEquals(4, $entity2->likesCount);
    }

    /** @test */
    public function it_can_recount_model_likes_with_morph_map_using_full_class_name()
    {
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

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'love:recount',
                'model' => EntityWithMorphMap::class,
                'type' => 'LIKE',
            ]),
            $output = new BufferedOutput
        );

        $this->assertEquals(0, $status);

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

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'love:recount',
                'type' => 'DISLIKE',
            ]),
            $output = new BufferedOutput
        );

        $this->assertEquals(0, $status);

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

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'love:recount',
                'model' => Entity::class,
                'type' => 'DISLIKE',
            ]),
            $output = new BufferedOutput
        );

        $this->assertEquals(0, $status);

        $likeCounter = LikeCounter::all();
        $this->assertCount(2, $likeCounter);
        $this->assertEquals(0, $entity1->likesCount);
        $this->assertEquals(3, $entity1->dislikesCount);
        $this->assertEquals(4, $entity2->dislikesCount);
    }

    /** @test */
    public function it_can_recount_model_dislikes_using_morph_map()
    {
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

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'love:recount',
                'model' => 'entity-with-morph-map',
                'type' => 'DISLIKE',
            ]),
            $output = new BufferedOutput
        );

        $this->assertEquals(0, $status);

        $likeCounter = LikeCounter::all();
        $this->assertCount(2, $likeCounter);
        $this->assertEquals(0, $entity1->likesCount);
        $this->assertEquals(3, $entity1->dislikesCount);
        $this->assertEquals(4, $entity2->dislikesCount);
    }

    /** @test */
    public function it_can_recount_model_dislikes_with_morph_map_using_full_class_name()
    {
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

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'love:recount',
                'model' => EntityWithMorphMap::class,
                'type' => 'DISLIKE',
            ]),
            $output = new BufferedOutput
        );

        $this->assertEquals(0, $status);

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

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'love:recount',
            ]),
            $output = new BufferedOutput
        );

        $this->assertEquals(0, $status);

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

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'love:recount',
                'model' => Entity::class,
            ]),
            $output = new BufferedOutput
        );

        $this->assertEquals(0, $status);

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

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'love:recount',
                'model' => 'entity-with-morph-map',
            ]),
            $output = new BufferedOutput
        );

        $this->assertEquals(0, $status);

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

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'love:recount',
                'model' => EntityWithMorphMap::class,
            ]),
            $output = new BufferedOutput
        );

        $this->assertEquals(0, $status);

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
        // TODO: Check if works on older Laravel versions. Otherwise uncomment assertContains on the end.
        $this->expectException(\Cog\Contracts\Love\Likeable\Exceptions\InvalidLikeable::class);

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'love:recount',
                'model' => 'not-exist-model',
            ]),
            $output = new BufferedOutput
        );

        $this->assertEquals(1, $status);
        //$this->assertContains('Cog\Contracts\Love\Likeable\Exceptions\InvalidLikeable', $output->fetch());
    }

    /** @test */
    public function it_can_throw_model_invalid_exception_if_class_not_implemented_has_likes_interface()
    {
        // TODO: Check if works on older Laravel versions. Otherwise uncomment assertContains on the end.
        $this->expectException(\Cog\Contracts\Love\Likeable\Exceptions\InvalidLikeable::class);

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'love:recount',
                'model' => User::class,
            ]),
            $output = new BufferedOutput
        );

        $this->assertEquals(1, $status);
        //$this->assertContains('Cog\Contracts\Love\Likeable\Exceptions\InvalidLikeable', $output->fetch());
    }

    public function it_deletes_records_before_recount()
    {
        // :TODO: Mock `removeLikeCountersOfType` method call
    }
}
