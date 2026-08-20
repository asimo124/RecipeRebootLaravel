<?php

namespace Database\Seeders;

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

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        foreach ([
            'ri_home_inventory',
            'ri_recipe_ingredient',
            'ri_recipe_attribute',
            'ri_related_ingredient',
            'ri_recipe',
            'ri_ingredient',
            'ri_ingredient_type',
            'ri_protein',
            'ri_recipe_style',
            'ri_attribute',
        ] as $table) {
            DB::table($table)->truncate();
        }

        $sql = File::get($path);

        // Prefer lookup tables before dependents regardless of dump order.
        $ordered = [
            'ri_ingredient_type',
            'ri_protein',
            'ri_recipe_style',
            'ri_attribute',
            'ri_ingredient',
            'ri_recipe',
            'ri_recipe_ingredient',
            'ri_recipe_attribute',
            'ri_home_inventory',
            'ri_related_ingredient',
        ];

        foreach ($ordered as $table) {
            if (preg_match('/INSERT INTO `'.$table.'`[\s\S]*?;/i', $sql, $match)) {
                DB::unprepared($match[0]);
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
