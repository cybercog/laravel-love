<?php

/*
 * This file is part of Laravel Likeable.
 *
 * (c) CyberCog <support@cybercog.su>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$factory->define(\Cog\Likeable\Tests\Stubs\Models\Entity::class, function (\Faker\Generator $faker) {
    return [
        'name' => $faker->name,
    ];
});
