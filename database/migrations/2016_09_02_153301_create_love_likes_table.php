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

/**
 * Class CreateLoveLikesTable.
 */
class CreateLoveLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('love_likes', function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('likeable');
            $table->integer('user_id')->unsigned()->index();
            $table->enum('type_id', [
                'LIKE',
                'DISLIKE',
            ])->default('LIKE');
            $table->timestamps();

            $table->unique([
                'likeable_type',
                'likeable_id',
                'user_id',
            ], 'like_user_unique');

            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('love_likes');
    }
}
