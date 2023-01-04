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

use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableInterface;
use Cog\Contracts\Love\Reacterable\Models\Reacterable as ReacterableInterface;
use Cog\Laravel\Love\Reaction\Models\Reaction;
use Cog\Laravel\Love\ReactionType\Models\ReactionType;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'love:upgrade-v5-to-v6', description: 'Upgrade Love package from v5 to v6')]
final class UpgradeV5ToV6 extends Command
{
    private Builder $queryBuilder;

    /**
     * Execute the console command.
     */
    public function handle(
        Builder $queryBuilder,
    ): int {
        $this->queryBuilder = $queryBuilder;

        $this->dbMigrate();
        $this->populateReactionTypes();
        $this->populateReacters();
        $this->populateReactants();
        $this->populateReactions();
        $this->dbCleanup();
        $this->filesystemCleanup();

        return self::SUCCESS;
    }

    private function dbMigrate(): void
    {
        $this->call('migrate');
    }

    private function dbCleanup(): void
    {
        $this->info('Deleting old database tables');
        DB::statement(
            <<<'SQL'
            DROP TABLE love_like_counters;
            SQL
        );
        DB::statement(
            <<<'SQL'
            DROP TABLE love_likes;
            SQL
        );
        DB::statement(
            <<<'SQL'
            DELETE FROM migrations
            WHERE migration = '2016_09_02_153301_create_love_likes_table'
            LIMIT 1;
            SQL
        );
        DB::statement(
            <<<'SQL'
            DELETE FROM migrations
            WHERE migration = '2016_09_02_163301_create_love_like_counters_table'
            LIMIT 1;
            SQL
        );
    }

    private function filesystemCleanup(): void
    {
        $this->info('Deleting old database migration files');
        $this->deleteMigrationFiles([
            '2016_09_02_153301_create_love_likes_table.php',
            '2016_09_02_163301_create_love_like_counters_table.php',
        ]);
    }

    private function populateReactionTypes(): void
    {
        $this->info('Populating Reaction Types');
        $names = $this->collectLikeTypes();
        $weights = [
            'Like' => 1,
            'Dislike' => -1,
        ];

        foreach ($names as $name) {
            $name = $this->reactionTypeNameFromLikeTypeName($name);

            if (!isset($weights[$name])) {
                $this->warn("Reaction weight for type `{$name}` not found.");
                continue;
            }

            if (ReactionType::query()->where('name', $name)->exists()) {
                continue;
            }

            ReactionType::query()->create([
                'name' => $name,
                'weight' => $weights[$name],
            ]);
        }
    }

    private function populateReacters(): void
    {
        $this->info('Populating Reacters');
        $classes = $this->collectLikerTypes();

        $reacterableClasses = [];
        foreach ($classes as $class) {
            if (!class_exists($class)) {
                $this->warn("Class `{$class}` is not found.");
                continue;
            }

            if (!in_array(ReacterableInterface::class, class_implements($class))) {
                $this->warn("Class `{$class}` need to implement Reacterable contract.");
                continue;
            }

            $reacterableClasses[] = $class;
        }

        foreach ($reacterableClasses as $class) {
            /** @var Collection<Model> $reacterables */
            $reacterables = $class::query()->get();
            $progress = $this->output->createProgressBar($reacterables->count());
            foreach ($reacterables as $reacterable) {
                if ($reacterable->getAttributeValue('love_reacter_id') > 0) {
                    $progress->advance();
                    continue;
                }

                $reacter = $reacterable->loveReacter()->create([
                    'type' => $reacterable->getMorphClass(),
                ]);
                $reacterable->setAttribute('love_reacter_id', $reacter->getId());
                $reacterable->save();
                $progress->advance();
            }
            $progress->finish();
        }
        $this->info('');
    }

    private function populateReactants(): void
    {
        $this->info('Populating Reactants');
        $classes = $this->collectLikeableTypes();

        $reactableClasses = [];
        foreach ($classes as $class) {
            $actualClass = Relation::getMorphedModel($class);
            if ($actualClass !== null) {
                $class = $actualClass;
            }

            if (!class_exists($class)) {
                $this->warn("Class `{$class}` is not found.");
                continue;
            }

            if (!in_array(ReactableInterface::class, class_implements($class))) {
                $this->warn("Class `{$class}` need to implement Reactable contract.");
                continue;
            }

            $reactableClasses[] = $class;
        }

        foreach ($reactableClasses as $class) {
            /** @var Collection<Model> $reactables */
            $reactables = $class::query()->get();
            $progress = $this->output->createProgressBar($reactables->count());
            foreach ($reactables as $reactable) {
                if ($reactable->getAttributeValue('love_reactant_id') > 0) {
                    $progress->advance();
                    continue;
                }

                $reactant = $reactable->loveReactant()->create([
                    'type' => $reactable->getMorphClass(),
                ]);
                $reactable->setAttribute('love_reactant_id', $reactant->getId());
                $reactable->save();
                $progress->advance();
            }
            $progress->finish();
        }
        $this->info('');
    }

    private function populateReactions(): void
    {
        $this->info('Converting Likes & Dislikes to Reactions');
        $likes = $this->queryBuilder
            ->newQuery()
            ->select('*')
            ->from('love_likes')
            ->orderBy('created_at', 'asc')
            ->get();

        $progress = $this->output->createProgressBar($likes->count());
        foreach ($likes as $like) {
            $class = $like->likeable_type;
            $actualClass = Relation::getMorphedModel($class);
            if ($actualClass !== null) {
                $class = $actualClass;
            }

            if (!class_exists($class)) {
                $this->warn("Class `{$class}` is not found.");
                $progress->advance();
                continue;
            }

            /** @var ReactableInterface $reactable */
            $reactable = $class::whereKey($like->likeable_id)->firstOrFail();

            $userClass = $this->getUserClass();

            if (!class_exists($class)) {
                $this->warn("Class `{$userClass}` is not found.");
                $progress->advance();
                continue;
            }

            /** @var ReacterableInterface $reacterable */
            $reacterable = $userClass::whereKey($like->user_id)->firstOrFail();
            $reactionTypeName = $this->reactionTypeNameFromLikeTypeName($like->type_id);

            $reactionTypeId = ReactionType::fromName($reactionTypeName)->getId();
            $reactantId = $reactable->getLoveReactant()->getId();
            $reacterId = $reacterable->getLoveReacter()->getId();

            $isReactionExists = Reaction::query()
                ->where('reaction_type_id', $reactionTypeId)
                ->where('reactant_id', $reactantId)
                ->where('reacter_id', $reacterId)
                ->exists();

            if ($isReactionExists) {
                $progress->advance();
                continue;
            }

            $reaction = new Reaction();
            $reaction->forceFill([
                'reaction_type_id' => $reactionTypeId,
                'reactant_id' => $reactantId,
                'reacter_id' => $reacterId,
                'created_at' => $like->created_at,
                'updated_at' => $like->updated_at,
            ]);
            $reaction->save();
            $progress->advance();
        }
        $progress->finish();
        $this->info('');
    }

    private function collectLikeableTypes(): iterable
    {
        return $this->queryBuilder
            ->newQuery()
            ->select('likeable_type')
            ->from('love_likes')
            ->groupBy('likeable_type')
            ->get()
            ->pluck('likeable_type');
    }

    private function collectLikerTypes(): array
    {
        return [
            $this->getUserClass(),
        ];
    }

    private function collectLikeTypes(): iterable
    {
        return $this->queryBuilder
            ->newQuery()
            ->select('type_id')
            ->from('love_likes')
            ->groupBy('type_id')
            ->get()
            ->pluck('type_id');
    }

    private function getUserClass(): string
    {
        $guard = config('auth.defaults.guard');
        $provider = config("auth.guards.{$guard}.provider");
        $class = config("auth.providers.{$provider}.model") ?? '';

        return $class;
    }

    private function reactionTypeNameFromLikeTypeName(
        string $name,
    ): string {
        return Str::studly(strtolower($name));
    }

    private function deleteMigrationFiles(
        array $files,
    ): void {
        foreach ($files as $file) {
            $file = database_path("migrations/{$file}");
            if (File::exists($file)) {
                File::delete($file);
            }
        }
    }
}
