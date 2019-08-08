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

use Doctrine\DBAL\Driver as DoctrineDbalDriver;
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
    protected $description = 'Upgrade Love package from v7 to v8';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->assertRequirements();
        $this->warn('Started Laravel Love v7 to v8 upgrade process.');
        $this->dbChangeReactionTypes();
        $this->dbChangeReactions();
        $this->dbChangeReactantReactionCounters();
        $this->dbChangeReactantReactionTotals();
        $this->info('Completed Laravel Love v7 to v8 upgrade process.');
    }

    private function assertRequirements(): void
    {
        if (interface_exists(DoctrineDbalDriver::class)) {
            return;
        }

        $this->error('Doctrine DBAL is missing!');
        $this->info('<comment>Install it with Composer:</comment> composer require doctrine/dbal');
        exit;
    }

    private function dbChangeReactionTypes(): void
    {
        $this->warn('DB: Renaming reaction types weight column.');
        $this->getDbSchema()->table('love_reaction_types', function (Blueprint $table) {
            $table->renameColumn('weight', 'mass');
        });
        $this->info('DB: Renamed reaction types weight column.');
    }

    private function dbChangeReactions(): void
    {
        $this->warn('DB: Adding rate column to reactions.');
        $this->getDbSchema()->table('love_reactions', function (Blueprint $table) {
            $table->decimal('rate', 4, 2)->after('reaction_type_id');
        });
        $this->info('DB: Added rate column to reactions.');

        $this->warn('DB: Updating reaction rate column values for existing records.');
        $this
            ->getDbQuery()
            ->table('love_reactions')
            ->where('rate', 0.0)
            ->update([
                'rate' => 1.0,
            ]);
        $this->info('DB: Updated reaction rate column values for existing records.');
    }

    private function dbChangeReactantReactionCounters(): void
    {
        $this->warn('DB: Changing default reaction counters values.');
        $this->getDbSchema()->table('love_reactant_reaction_counters', function (Blueprint $table) {
            $table->unsignedBigInteger('count')->default(null)->change();
            $table->decimal('weight', 13, 2)->default(null)->change();
        });
        $this->info('DB: Changed default reaction counters values.');
    }

    private function dbChangeReactantReactionTotals(): void
    {
        $this->warn('DB: Changing default reaction totals values.');
        $this->getDbSchema()->table('love_reactant_reaction_totals', function (Blueprint $table) {
            $table->unsignedBigInteger('count')->default(null)->change();
            $table->decimal('weight', 13, 2)->default(null)->change();
        });
        $this->info('DB: Changed default reaction counters values.');
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
