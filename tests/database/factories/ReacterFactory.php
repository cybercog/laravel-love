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

use Cog\Laravel\Love\Reacter\Models\Reacter;
use Cog\Tests\Laravel\Love\Stubs\Models\User;
use Faker\Generator as Faker;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Reacter::class, function (Faker $faker) {
    return [
        'type' => (new User())->getMorphClass(),
    ];
});

$factory->afterCreatingState(Reacter::class, 'withReacterable', function (Reacter $reacter, Faker $faker) {
    factory(User::class)->create([
        'love_reacter_id' => $reacter->getId(),
    ]);
});
