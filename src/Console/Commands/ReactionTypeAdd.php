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
use Illuminate\Support\Str;

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
     * @return int
     */
    public function handle(
        Dispatcher $events
    ): int {
        if ($this->option('default')) {
            $this->createDefaultReactionTypes();

            return 0;
        }

        $name = $this->resolveName();
        if ($this->isReactionTypeNameExists($name)) {
            $this->error(sprintf(
                'Reaction type with name `%s` already exists.',
                $name
            ));

            return 1;
        }

        $this->createReactionType($name, $this->resolveWeight());

        return 0;
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
            if ($this->isReactionTypeNameExists($type['name'])) {
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
            'Reaction type with name `%s` and weight `%d` was added.',
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

        return Str::studly($name);
    }

    private function resolveWeight(): int
    {
        return intval($this->argument('weight') ?? $this->ask('What is the weight of this reaction type?'));
    }

    private function isReactionTypeNameExists(string $name): bool
    {
        return ReactionType::query()->where('name', $name)->exists();
    }
}
