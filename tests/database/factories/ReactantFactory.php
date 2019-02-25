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

use Cog\Laravel\Love\Reactant\Models\Reactant;
use Cog\Tests\Laravel\Love\Stubs\Models\Article;
use Faker\Generator as Faker;

/* @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(Reactant::class, function (Faker $faker) {
    return [
        'type' => (new Article())->getMorphClass(),
    ];
});

$factory->afterCreatingState(Reactant::class, 'withReactable', function (Reactant $reactant, Faker $faker) {
    factory(Article::class)->create([
        'love_reactant_id' => $reactant->getId(),
    ]);
});
