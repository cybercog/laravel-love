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

namespace Cog\Laravel\Love\Support\Database\Eloquent;

use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class Model extends EloquentModel
{
    /**
     * Get the current connection name for the model.
     *
     * @return string
     */
    public function getConnectionName(): ?string
    {
        return defined('COG_LOVE_DB_CONNECTION')
            ? COG_LOVE_DB_CONNECTION
            : $this->connection;
    }
}
