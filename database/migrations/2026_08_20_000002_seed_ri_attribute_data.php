<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
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

        foreach ($attributes as $attribute) {
            $existing = DB::table('ri_attribute')->where('title', $attribute['title'])->first();

            if ($existing) {
                DB::table('ri_attribute')
                    ->where('id', $existing->id)
                    ->update(['severity_level' => $attribute['severity_level']]);
            } else {
                DB::table('ri_attribute')->insert($attribute);
            }
        }
    }

    public function down(): void
    {
        DB::table('ri_attribute')->whereIn('title', [
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
