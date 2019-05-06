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

final class ReactionTypeAdd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'love:reaction-type-add
                            {--default}
                            {name?}
                            {weight?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Reaction Type to Laravel Love';

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Contracts\Events\Dispatcher $events
     * @return void
     */
    public function handle(
        Dispatcher $events
    ): void {
        if ($this->option('default')) {
            $this->createDefaultReactionTypes();

            return;
        }

        $this->createReactionType($this->resolveName(), $this->resolveWeight());
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

            $this->createReactionType($type['name'], $type['weight']);
        }
    }

    private function createReactionType(string $name, int $weight): void
    {
        ReactionType::query()->create([
            'name' => $name,
            'weight' => $weight,
        ]);

        $this->line(sprintf(
            "Reaction type with name `%s` and weight `%d` was added.",
            $name,
            $weight
        ));
    }

    private function resolveName(): string
    {
        $name = $this->argument('name') ?? $this->ask('How to name reaction type?');

        if (is_null($name)) {
            $name = $this->resolveName();
        }

        return $name;
    }

    private function resolveWeight(): int
    {
        return intval($this->argument('weight') ?? $this->ask('What is the weight of this reaction type?'));
    }
}
