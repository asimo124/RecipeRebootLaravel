<?php

namespace App\Console\Commands;

use App\Models\RecipeModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CopyRecipeTablesToPgsql extends Command
{
    protected $signature = 'recipes:copy-mysql-to-pgsql
                            {--force : Allow running in production}
                            {--dry-run : Show row counts without copying}';

    protected $description = 'Copy ri_* recipe tables from MySQL into PostgreSQL';

    public function handle(): int
    {
        if ($this->laravel->environment('production') && ! $this->option('force')) {
            $this->error('Refusing to copy in production without --force.');

            return self::FAILURE;
        }

        $mysql = DB::connection(config('database.default'));
        $pgsql = DB::connection(RecipeModel::CONNECTION_NAME);

        if ($mysql->getDriverName() === 'pgsql') {
            $this->error('Default DB connection is already pgsql. Set DB_CONNECTION=mysql so MySQL remains the source.');

            return self::FAILURE;
        }

        $this->info('Source: '.$mysql->getName().' ('.$mysql->getDatabaseName().')');
        $this->info('Target: '.$pgsql->getName().' ('.$pgsql->getDatabaseName().')');

        $missing = [];
        foreach (RecipeModel::TABLES_IN_FK_ORDER as $table) {
            $mysqlCount = Schema::connection($mysql->getName())->hasTable($table)
                ? $mysql->table($table)->count()
                : null;
            $pgsqlCount = Schema::connection($pgsql->getName())->hasTable($table)
                ? $pgsql->table($table)->count()
                : null;

            if ($mysqlCount === null) {
                $missing[] = $table;
                $this->warn("  {$table}: missing on MySQL");
                continue;
            }

            $pgsqlLabel = $pgsqlCount === null ? 'not created yet' : (string) $pgsqlCount;
            $this->line("  {$table}: mysql={$mysqlCount} pgsql={$pgsqlLabel}");
        }

        if ($missing) {
            $this->error('MySQL is missing recipe tables: '.implode(', ', $missing));

            return self::FAILURE;
        }

        if ($this->option('dry-run')) {
            $this->comment('Dry run only — no data copied.');

            return self::SUCCESS;
        }

        foreach (RecipeModel::TABLES_IN_FK_ORDER as $table) {
            if (! Schema::connection($pgsql->getName())->hasTable($table)) {
                $this->error("PostgreSQL table {$table} does not exist. Run `php artisan migrate` first.");

                return self::FAILURE;
            }
        }

        $pgsql->statement('SET session_replication_role = replica');

        try {
            $pgsql->statement(
                'TRUNCATE TABLE '.implode(', ', RecipeModel::TABLES_IN_FK_ORDER).' RESTART IDENTITY CASCADE'
            );

            foreach (RecipeModel::TABLES_IN_FK_ORDER as $table) {
                $copied = 0;
                $mysql->table($table)->orderBy('id')->chunkById(500, function ($rows) use ($pgsql, $table, &$copied) {
                    $payload = $rows->map(fn ($row) => (array) $row)->all();
                    if ($payload === []) {
                        return;
                    }
                    $pgsql->table($table)->insert($payload);
                    $copied += count($payload);
                });

                $this->resetSequence($pgsql, $table);
                $this->info("Copied {$copied} rows into {$table}");
            }
        } finally {
            $pgsql->statement('SET session_replication_role = DEFAULT');
        }

        $this->info('Recipe tables copied from MySQL to PostgreSQL.');

        return self::SUCCESS;
    }

    private function resetSequence($pgsql, string $table): void
    {
        $max = $pgsql->table($table)->max('id');
        if (! $max) {
            return;
        }

        $pgsql->statement(
            "SELECT setval(pg_get_serial_sequence(?, 'id'), ?)",
            [$table, (int) $max]
        );
    }
}
