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

namespace Cog\Laravel\Love\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

final class UpgradeV7ToV8 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'love:upgrade-v7-to-v8';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade love package from v7 to v8';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->dbChangeReactionType();
    }

    private function dbChangeReactionType(): void
    {
        $this->getDbSchema()->table('reaction_types', function (Blueprint $table) {
            $table->renameColumn('weight', 'mass');
        });
    }

    private function getDbSchema(): Builder
    {
        return Schema::connection(Config::get('love.storage.database.connection'));
    }
}
