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
        if (! Schema::connection(RecipeModel::CONNECTION_NAME)->hasTable('ri_ingredient_type')) {
            return;
        }

        $db = DB::connection(RecipeModel::CONNECTION_NAME);

        $exists = $db->table('ri_ingredient_type')->where('title', 'Fat')->exists();

        if (! $exists) {
            $db->table('ri_ingredient_type')->insert(['title' => 'Fat']);
        }
    }

    public function down(): void
    {
        if (! Schema::connection(RecipeModel::CONNECTION_NAME)->hasTable('ri_ingredient_type')) {
            return;
        }

        DB::connection(RecipeModel::CONNECTION_NAME)
            ->table('ri_ingredient_type')
            ->where('title', 'Fat')
            ->delete();
    }
};
