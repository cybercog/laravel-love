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

namespace Cog\Laravel\Love\Console\Commands;

use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Illuminate\Console\Command;
use Illuminate\Contracts\Events\Dispatcher;

final class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'love:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Laravel Love';

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     * @return void
     */
    public function handle(
        Dispatcher $events
    ): void {
        $this->createDefaultReactionTypes();
    }

    private function createDefaultReactionTypes(): void
    {
        $types = [
            [
                'name' => 'Like',
                'weight' => 1,
            ],
            [
                'name' => 'Dislike',
                'weight' => -1,
            ],
        ];

        foreach ($types as $type) {
            if (ReactionType::query()->where('name', $type['name'])->exists()) {
                continue;
            }

            ReactionType::query()->create($type);
        }
    }
}
