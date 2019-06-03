<?php

/*
 * This file is part of Laravel Love.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class AddForeignColumn extends Migration
{
    public function up(): void
    {
        Schema::table('{table}', function (Blueprint $table) {
            $table->unsignedBigInteger('{column}');

            $table
                ->foreign('{column}')
                ->references('id')
                ->on('{foreignTable}');
        });
    }

    public function down(): void
    {
        Schema::table('{table}', function (Blueprint $table) {
            $table->dropColumn('{column}');
        });
    }
}
