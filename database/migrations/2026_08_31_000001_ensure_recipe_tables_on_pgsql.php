<?php

use App\Support\RecipeBookSchema;
use App\Support\RecipePgsql;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        RecipePgsql::assertConfigured();
        RecipeBookSchema::createCoreTables();
        RecipeBookSchema::createAttributeTables();
    }

    public function down(): void
    {
        RecipeBookSchema::dropAttributeTables();
        RecipeBookSchema::dropCoreTables();
    }
};
