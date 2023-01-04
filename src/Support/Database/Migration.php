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
use Illuminate\Support\Facades\Schema;

abstract class Migration extends IlluminateMigration
{
    /**
     * The database schema.
     *
     * @var \Illuminate\Database\Schema\Builder
     */
    protected $schema;

    public function __construct()
    {
        $this->schema = Schema::connection($this->getConnection());
    }

    /**
     * Get the migration connection name.
     */
    public function getConnection(): ?string
    {
        return Config::get('love.storage.database.connection');
    }
}
