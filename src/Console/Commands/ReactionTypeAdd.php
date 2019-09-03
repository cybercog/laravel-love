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

namespace Cog\Laravel\Love\Console\Commands;

use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

final class ReactionTypeAdd extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'love:reaction-type-add';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Reaction Type to Laravel Love';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        if ($this->option('default')) {
            $this->createDefaultReactionTypes();

            return 0;
        }

        $name = $this->resolveName();
        $name = $this->sanitizeName($name);

        if ($this->isNameInvalid($name)) {
            $this->error(sprintf(
                'Reaction type with name `%s` is invalid.',
                $name
            ));

            return 1;
        }

        if ($this->isReactionTypeNameExists($name)) {
            $this->error(sprintf(
                'Reaction type with name `%s` already exists.',
                $name
            ));

            return 1;
        }

        $this->createReactionType($name, $this->resolveMass());

        return 0;
    }

    protected function getOptions(): array
    {
        return [
            ['default', null, InputOption::VALUE_NONE, 'Create default Like & Dislike reactions'],
            ['name', null, InputOption::VALUE_OPTIONAL, 'The name of the reaction'],
            ['mass', null, InputOption::VALUE_OPTIONAL, 'The mass of the reaction'],
        ];
    }

    private function createDefaultReactionTypes(): void
    {
        $types = [
            [
                'name' => 'Like',
                'mass' => 1,
            ],
            [
                'name' => 'Dislike',
                'mass' => -1,
            ],
        ];

        foreach ($types as $type) {
            if ($this->isReactionTypeNameExists($type['name'])) {
                $this->line(sprintf(
                    'Reaction type with name `%s` already exists.',
                    $type['name']
                ));
                continue;
            }

            $this->createReactionType($type['name'], $type['mass']);
        }
    }

    private function createReactionType(string $name, int $mass): void
    {
        ReactionType::query()->create([
            'name' => $name,
            'mass' => $mass,
        ]);

        $this->line(sprintf(
            'Reaction type with name `%s` and mass `%d` was added.',
            $name, $mass
        ));
    }

    private function resolveName(): string
    {
        return $this->option('name')
            ?? $this->ask('How to name reaction type?')
            ?? '';
    }

    private function resolveMass(): int
    {
        $mass = $this->option('mass')
            ?? $this->ask('What is the mass of this reaction type?')
            ?? ReactionType::MASS_DEFAULT;

        return intval($mass);
    }

    private function sanitizeName(string $name): string
    {
        $name = trim($name);
        $name = Str::studly($name);

        return $name;
    }

    private function isReactionTypeNameExists(string $name): bool
    {
        return ReactionType::query()
            ->where('name', $name)
            ->exists();
    }

    private function isNameInvalid(string $name): bool
    {
        return preg_match('#^[A-Z][a-zA-Z0-9_]*$#', $name) === 0;
    }
}
