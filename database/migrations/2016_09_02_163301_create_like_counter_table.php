<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Class CreateLikeCounterTable.
 */
class CreateLikeCounterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('like_counter', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('likeable_id')->unsigned();
            $table->string('likeable_type');
            $table->enum('type_id', [
                'like',
                'dislike',
            ])->default('like');
            $table->integer('count')->unsigned()->default(0);

            $table->unique([
                'likeable_id',
                'likeable_type',
                'type_id',
            ], 'like_counter_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('like_counter');
    }
}
