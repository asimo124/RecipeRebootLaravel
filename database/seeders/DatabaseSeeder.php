<?php

namespace Database\Seeders;

use App\Models\RecipeModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);

        $path = database_path('seeders/data/api_db_dump.sql');

        if (! File::exists($path)) {
            $this->command?->error("Seed dump not found at {$path}");

            return;
        }

        $pgsql = DB::connection(RecipeModel::CONNECTION_NAME);
        $tables = RecipeModel::TABLES_IN_FK_ORDER;

        $pgsql->statement('TRUNCATE TABLE '.implode(', ', $tables).' RESTART IDENTITY CASCADE');

        $sql = str_replace('`', '', File::get($path));

        foreach ($tables as $table) {
            if (preg_match('/INSERT INTO '.$table.'[\s\S]*?;/i', $sql, $match)) {
                $pgsql->unprepared($match[0]);
            }
        }

        foreach ($tables as $table) {
            $max = $pgsql->table($table)->max('id');
            if ($max) {
                $pgsql->statement(
                    "SELECT setval(pg_get_serial_sequence(?, 'id'), ?)",
                    [$table, (int) $max]
                );
            }
        }
    }
}
