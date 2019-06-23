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

namespace Cog\Laravel\Love\Support\Database;

use Illuminate\Database\Migrations\Migration as IlluminateMigration;
use Illuminate\Support\Facades\Config;

abstract class Migration extends IlluminateMigration
{
    /**
     * Get the migration connection name.
     *
     * @return null|string
     */
    public function getConnection(): ?string
    {
        return Config::get('love.storage.database.connection');
    }
}
