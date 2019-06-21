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

use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Faker\Generator as Faker;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(ReactionType::class, function (Faker $faker) {
    return [
        'name' => implode('', $faker->words),
        'weight' => $faker->numberBetween(-128, 127),
    ];
});
