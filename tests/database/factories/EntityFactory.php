<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) Anton Komarev <a.komarev@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Cog\Likeable\Tests\Stubs\Models\Entity;
use Faker\Generator;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Entity::class, function (Generator $faker) {
    return [
        'name' => $faker->name,
    ];
});
