<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cog\Likeable\Tests\Unit\Observers;

use Cog\Likeable\Models\LikeCounter;
use Cog\Likeable\Tests\Stubs\Models\Article;
use Cog\Likeable\Tests\Stubs\Models\Entity;
use Cog\Likeable\Tests\Stubs\Models\EntityWithMorphMap;
use Cog\Likeable\Tests\Stubs\Models\User;
use Cog\Likeable\Tests\TestCase;
use Illuminate\Contracts\Console\Kernel;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class LikeableRecountCommandTest.
 *
 * @package Cog\Likeable\Tests\Unit\Console
 */
class LikeableRecountCommandTest extends TestCase
{
    protected $kernel;

    public function setUp()
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

        $entity1->dislike(9);
        $entity1->like(1);
        $entity1->like(7);
        $entity1->like(8);
        $entity2->like(1);
        $entity2->like(2);
        $entity2->like(3);
        $entity2->like(4);
        $article->like(4);

        LikeCounter::truncate();

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'likeable:recount',
                'type' => 'like',
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

        $entity1->dislike(9);
        $entity1->like(1);
        $entity1->like(7);
        $entity1->like(8);
        $entity2->like(1);
        $entity2->like(2);
        $entity2->like(3);
        $entity2->like(4);

        LikeCounter::truncate();

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'likeable:recount',
                'model' => Entity::class,
                'type' => 'like',
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

        $entity1->dislike(9);
        $entity1->like(1);
        $entity1->like(7);
        $entity1->like(8);
        $entity2->like(1);
        $entity2->like(2);
        $entity2->like(3);
        $entity2->like(4);

        LikeCounter::truncate();

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'likeable:recount',
                'model' => 'entity-with-morph-map',
                'type' => 'like',
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

        $entity1->dislike(9);
        $entity1->like(1);
        $entity1->like(7);
        $entity1->like(8);
        $entity2->like(1);
        $entity2->like(2);
        $entity2->like(3);
        $entity2->like(4);

        LikeCounter::truncate();

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'likeable:recount',
                'model' => EntityWithMorphMap::class,
                'type' => 'like',
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

        $entity1->like(9);
        $entity1->dislike(1);
        $entity1->dislike(7);
        $entity1->dislike(8);
        $entity2->dislike(1);
        $entity2->dislike(2);
        $entity2->dislike(3);
        $entity2->dislike(4);
        $article->dislike(4);

        LikeCounter::truncate();

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'likeable:recount',
                'type' => 'dislike',
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

        $entity1->like(9);
        $entity1->dislike(1);
        $entity1->dislike(7);
        $entity1->dislike(8);
        $entity2->dislike(1);
        $entity2->dislike(2);
        $entity2->dislike(3);
        $entity2->dislike(4);

        LikeCounter::truncate();

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'likeable:recount',
                'model' => Entity::class,
                'type' => 'dislike',
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

        $entity1->like(9);
        $entity1->dislike(1);
        $entity1->dislike(7);
        $entity1->dislike(8);
        $entity2->dislike(1);
        $entity2->dislike(2);
        $entity2->dislike(3);
        $entity2->dislike(4);

        LikeCounter::truncate();

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'likeable:recount',
                'model' => 'entity-with-morph-map',
                'type' => 'dislike',
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

        $entity1->like(9);
        $entity1->dislike(1);
        $entity1->dislike(7);
        $entity1->dislike(8);
        $entity2->dislike(1);
        $entity2->dislike(2);
        $entity2->dislike(3);
        $entity2->dislike(4);

        LikeCounter::truncate();

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'likeable:recount',
                'model' => EntityWithMorphMap::class,
                'type' => 'dislike',
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

        $entity1->like(9);
        $entity1->like(7);
        $entity1->dislike(8);
        $entity2->like(1);
        $entity2->like(2);
        $entity2->dislike(3);
        $entity2->dislike(4);
        $article->dislike(4);

        LikeCounter::truncate();

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'likeable:recount',
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

        $entity1->like(9);
        $entity1->like(7);
        $entity1->dislike(8);
        $entity2->like(1);
        $entity2->like(2);
        $entity2->dislike(3);
        $entity2->dislike(4);

        LikeCounter::truncate();

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'likeable:recount',
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

        $entity1->like(1);
        $entity1->like(7);
        $entity1->dislike(8);
        $entity2->like(1);
        $entity2->like(2);
        $entity2->dislike(3);
        $entity2->dislike(4);

        LikeCounter::truncate();

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'likeable:recount',
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

        $entity1->like(1);
        $entity1->like(7);
        $entity1->dislike(8);
        $entity2->like(1);
        $entity2->like(2);
        $entity2->dislike(3);
        $entity2->dislike(4);

        LikeCounter::truncate();

        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'likeable:recount',
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
        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'likeable:recount',
                'model' => 'not-exist-model',
            ]),
            $output = new BufferedOutput
        );

        $this->assertEquals(1, $status);
        $this->assertContains('Cog\Likeable\Exceptions\ModelInvalidException', $output->fetch());
    }

    /** @test */
    public function it_can_throw_model_invalid_exception_if_class_not_implemented_has_likes_interface()
    {
        $status = $this->kernel->handle(
            $input = new ArrayInput([
                'command' => 'likeable:recount',
                'model' => User::class,
            ]),
            $output = new BufferedOutput
        );

        $this->assertEquals(1, $status);
        $this->assertContains('Cog\Likeable\Exceptions\ModelInvalidException', $output->fetch());
    }

    public function it_deletes_records_before_recount()
    {
        // :TODO: Mock `removeLikeCountersOfType` method call
    }
}
