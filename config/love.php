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

return [

    /*
    |--------------------------------------------------------------------------
    | Love Storage Driver
    |--------------------------------------------------------------------------
    |
    | This configuration options determines the storage driver that will
    | be used to store Love's data. In addition, you may set any
    | custom options as needed by the particular driver you choose.
    |
    */

    'storage' => [
        'database' => [
            'connection' => env('DB_CONNECTION', 'mysql'),
            'tables' => [
                'love_reacters' => null,
                'love_reactants' => null,
                'love_reaction_types' => null,
                'love_reactions' => null,
                'love_reactant_reaction_counters' => null,
                'love_reactant_reaction_totals' => null,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Love Database Migrations
    |--------------------------------------------------------------------------
    |
    | Determine if default package migrations should be registered.
    | Set value to `false` when using customized migrations.
    |
    */

    'load_default_migrations' => true,

];
