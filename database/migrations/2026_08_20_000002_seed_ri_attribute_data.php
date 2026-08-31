<?php

use App\Models\RecipeModel;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'pgsql';

    public function up(): void
    {
        if (! Schema::connection(RecipeModel::CONNECTION_NAME)->hasTable('ri_attribute')) {
            return;
        }

        $attributes = [
            ['title' => 'Gluten', 'severity_level' => 3],
            ['title' => 'Beef', 'severity_level' => 3],
            ['title' => 'Tangy Sauce', 'severity_level' => 3],
            ['title' => 'Heavy Spices', 'severity_level' => 2],
            ['title' => 'Spicy', 'severity_level' => 2],
            ['title' => 'Sweet', 'severity_level' => 2],
            ['title' => 'Cheese', 'severity_level' => 2],
            ['title' => 'Sour Cream', 'severity_level' => 2],
        ];

        $db = DB::connection(RecipeModel::CONNECTION_NAME);

        foreach ($attributes as $attribute) {
            $existing = $db->table('ri_attribute')->where('title', $attribute['title'])->first();

            if ($existing) {
                $db->table('ri_attribute')
                    ->where('id', $existing->id)
                    ->update(['severity_level' => $attribute['severity_level']]);
            } else {
                $db->table('ri_attribute')->insert($attribute);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::connection(RecipeModel::CONNECTION_NAME)->hasTable('ri_attribute')) {
            return;
        }

        DB::connection(RecipeModel::CONNECTION_NAME)->table('ri_attribute')->whereIn('title', [
            'Gluten',
            'Beef',
            'Tangy Sauce',
            'Heavy Spices',
            'Spicy',
            'Sweet',
            'Cheese',
            'Sour Cream',
        ])->delete();
    }
};
