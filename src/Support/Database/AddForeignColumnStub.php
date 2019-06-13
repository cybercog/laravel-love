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

namespace Cog\Laravel\Love\Support\Database;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

final class AddForeignColumnStub
{
    private $files;

    private $table;

    private $referencedTable;

    private $foreignColumn;

    private $isForeignColumnNullable;

    private $referencedColumn;

    public function __construct(
        Filesystem $files,
        string $table,
        string $referencedTable,
        string $foreignColumn,
        string $referencedColumn,
        bool $isForeignColumnNullable
    ) {
        $this->files = $files;
        $this->table = $table;
        $this->referencedTable = $referencedTable;
        $this->foreignColumn = $foreignColumn;
        $this->referencedColumn = $referencedColumn;
        $this->isForeignColumnNullable = $isForeignColumnNullable;
    }

    public function getFilename(): string
    {
        return sprintf('add_%s_to_%s_table', $this->foreignColumn, $this->table);
    }

    public function getClass(): string
    {
        return Str::studly($this->getFilename());
    }

    private function getStubsPath(): string
    {
        return __DIR__ . '/Stubs';
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function getStub(): string
    {
        $filename = $this->isForeignColumnNullable
            ? 'AddForeignNullableColumn.stub'
            : 'AddForeignColumn.stub';

        return $this->files->get("{$this->getStubsPath()}/{$filename}");
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function getPopulatedContent(): string
    {
        $stub = $this->getStub();

        $stub = str_replace('DummyClass', $this->getClass(), $stub);
        $stub = str_replace('DummyTable', $this->table, $stub);
        $stub = str_replace('DummyForeignColumn', $this->foreignColumn, $stub);
        $stub = str_replace('DummyReferencedTable', $this->referencedTable, $stub);
        $stub = str_replace('DummyReferencedColumn', $this->referencedColumn, $stub);

        return $stub;
    }
}
