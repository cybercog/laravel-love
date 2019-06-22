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

use Illuminate\Database\Migrations\Migration as BaseMigration;

abstract class Migration extends BaseMigration
{
    /**
     * Get the migration connection name.
     *
     * @return string
     */
    public function getConnection(): ?string
    {
        return defined('COG_LOVE_DB_CONNECTION')
            ? COG_LOVE_DB_CONNECTION
            : $this->connection;
    }
}
