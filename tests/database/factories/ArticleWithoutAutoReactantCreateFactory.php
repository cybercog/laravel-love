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

use Cog\Tests\Laravel\Love\Stubs\Models\ArticleWithoutAutoReactantCreate;
use Faker\Generator as Faker;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(ArticleWithoutAutoReactantCreate::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
