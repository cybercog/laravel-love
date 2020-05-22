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

use Illuminate\Database\Eloquent\Model as IlluminateModel;
use Illuminate\Support\Facades\Config;

abstract class Model extends IlluminateModel
{
    /**
     * Get the current connection name for the model.
     *
     * @return null|string
     */
    public function getConnectionName(): ?string
    {
        return Config::get('love.storage.database.connection');
    }

    public function getTable(): string
    {
        return Config::get("love.storage.database.tables.{$this->table}")
            ?? $this->table;
    }
}
