<?php

/*
 * This file is part of Laravel Love.
 *
 * (c) Anton Komarev <anton@komarev.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Cog\Tests\Laravel\Love\Stubs\Models;

use Cog\Laravel\Love\Reactable\ReactableEloquentBuilderTrait;
use Illuminate\Database\Eloquent\Builder;

final class ArticleEloquentBuilder extends Builder
{
    use ReactableEloquentBuilderTrait;
}
