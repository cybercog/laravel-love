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
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
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
        $this->dbChangeReactionTypes();
        $this->dbChangeReactions();
        $this->dbChangeReactantReactionCounters();
        $this->dbChangeReactantReactionTotals();
    }

    private function dbChangeReactionTypes(): void
    {
        $this->getDbSchema()->table('love_reaction_types', function (Blueprint $table) {
            $table->renameColumn('weight', 'mass');
        });
    }

    private function dbChangeReactions(): void
    {
        $this->getDbSchema()->table('love_reactions', function (Blueprint $table) {
            $table->decimal('rate', 4, 2)->after('reaction_type_id');
        });

        $this
            ->getDbQuery()
            ->table('love_reactions')
            ->where('rate', 0.0)
            ->update([
                'rate' => 1.0,
            ]);
    }

    private function dbChangeReactantReactionCounters(): void
    {
        $this->getDbSchema()->table('love_reactant_reaction_counters', function (Blueprint $table) {
            $table->unsignedBigInteger('count')->default(null)->change();
            $table->decimal('weight', 13, 2)->default(null)->change();
        });
    }

    private function dbChangeReactantReactionTotals(): void
    {
        $this->getDbSchema()->table('love_reactant_reaction_totals', function (Blueprint $table) {
            $table->unsignedBigInteger('count')->default(null)->change();
            $table->decimal('weight', 13, 2)->default(null)->change();
        });
    }

    private function getDbSchema(): Builder
    {
        return Schema::connection($this->getDatabaseConnection());
    }

    private function getDbQuery(): ConnectionInterface
    {
        return DB::connection($this->getDatabaseConnection());
    }

    private function getDatabaseConnection(): ?string
    {
        return Config::get('love.storage.database.connection');
    }
}
