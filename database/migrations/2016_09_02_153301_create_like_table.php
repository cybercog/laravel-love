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

class CreateLikeTable extends Migration
{
    public function up()
    {
        Schema::create('like', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('likeable_id')->unsigned();
            $table->string('likeable_type', 255);
            $table->integer('user_id')->unsigned()->index();
            $table->enum('type_id', [
                'like',
                'dislike',
            ])->default('like');
            $table->timestamp('created_at')->nullable();

            $table->unique([
                'likeable_id',
                'likeable_type',
                'user_id',
            ], 'like_user_unique');

            $table->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::drop('like');
    }
}
