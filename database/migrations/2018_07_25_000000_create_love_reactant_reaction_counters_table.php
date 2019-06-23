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

use Cog\Laravel\Love\Support\Database\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

final class CreateLoveReactantReactionCountersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('love_reactant_reaction_counters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('reactant_id');
            $table->unsignedBigInteger('reaction_type_id');
            $table->unsignedBigInteger('count')->default(0);
            $table->bigInteger('weight')->default(0);
            $table->timestamps();

            $table->index([
                'reactant_id',
                'reaction_type_id',
            ], 'love_reactant_reaction_counters_reactant_reaction_type_index');

            $table
                ->foreign('reactant_id')
                ->references('id')
                ->on('love_reactants')
                ->onDelete('cascade');
            $table
                ->foreign('reaction_type_id')
                ->references('id')
                ->on('love_reaction_types')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('love_reactant_reaction_counters');
    }
}
