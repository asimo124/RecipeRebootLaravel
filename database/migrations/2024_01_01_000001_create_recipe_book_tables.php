<?php

use App\Support\RecipeBookSchema;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    protected $connection = 'pgsql';

    public function up(): void
    {
        RecipeBookSchema::createCoreTables();
    }

    public function down(): void
    {
        RecipeBookSchema::dropCoreTables();
    }
};
