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

final class CreateLoveReactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $this->schema->create('love_reactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('reactant_id');
            $table->unsignedBigInteger('reacter_id');
            $table->unsignedBigInteger('reaction_type_id');
            $table->decimal('rate', 4, 2);
            $table->timestamps();

            $table->index([
                'reactant_id',
                'reaction_type_id',
            ]);
            $table->index([
                'reactant_id',
                'reacter_id',
                'reaction_type_id',
            ]);
            $table->index([
                'reactant_id',
                'reacter_id',
            ]);
            $table->index([
                'reacter_id',
                'reaction_type_id',
            ]);

            $table
                ->foreign('reactant_id')
                ->references('id')
                ->on('love_reactants')
                ->onDelete('cascade');
            $table
                ->foreign('reacter_id')
                ->references('id')
                ->on('love_reacters')
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
        $this->schema->dropIfExists('love_reactions');
    }
}
