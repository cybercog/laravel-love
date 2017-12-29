<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cog\Tests\Likeable\Stubs\Models\Entity;
use Faker\Generator as Faker;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Entity::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
