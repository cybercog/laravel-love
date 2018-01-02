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

namespace Cog\Tests\Laravel\Love\Unit\Like\Observers;

use Cog\Tests\Laravel\Love\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Class LikeObserverTest.
 *
 * @package Cog\Tests\Laravel\Love\Unit\Like\Observers
 */
class LikeObserverTest extends TestCase
{
    use DatabaseTransactions;
}
