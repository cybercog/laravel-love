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

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableInterface;
use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableInterface;
use Cog\Laravel\Love\Reactable\Models\Traits\Reactable;
use Cog\Laravel\Love\Reacterable\Models\Traits\Reacterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Person extends Model implements
    ReacterableInterface,
    ReactableInterface
{
    use HasFactory;
    use Reactable;
    use Reacterable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'people';
}
