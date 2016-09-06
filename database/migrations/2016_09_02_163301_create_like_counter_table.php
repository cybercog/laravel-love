<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) CyberCog <support@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLikeCounterTable extends Migration
{
    public function up()
    {
        Schema::create('like_counter', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('likeable_id')->unsigned();
            $table->string('likeable_type');
            $table->enum('type_id', [
                1 => 'like',
                2 => 'dislike',
            ])->default(1);
            $table->integer('count')->unsigned()->default(0);

            $table->unique([
                'likeable_id',
                'likeable_type',
                'type_id',
            ], 'like_counter_unique');
        });
    }

    public function down()
    {
        Schema::drop('like_counter');
    }
}
