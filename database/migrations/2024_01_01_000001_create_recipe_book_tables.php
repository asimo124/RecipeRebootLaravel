<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ri_ingredient_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
        });

        Schema::create('ri_protein', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
        });

        Schema::create('ri_recipe_style', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
        });

        Schema::create('ri_ingredient', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->unsignedInteger('ingredient_type_id')->nullable();
            $table->float('price', 10, 2)->nullable();
            $table->float('cheap_price', 10, 2)->nullable();
            $table->unsignedInteger('store_section_id')->nullable();

            $table->index('ingredient_type_id');
            $table->foreign('ingredient_type_id')
                ->references('id')
                ->on('ri_ingredient_type')
                ->nullOnDelete();
        });

        Schema::create('ri_recipe', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->tinyInteger('is_deleted')->default(0);
            $table->date('last_date_made')->nullable();
            $table->tinyInteger('contains_gluten')->default(0)->nullable();
            $table->string('image_path')->nullable();
            $table->unsignedInteger('protein_id')->nullable();
            $table->unsignedInteger('recipe_style_id')->nullable();
            $table->string('recipe_link')->nullable();

            $table->foreign('protein_id')->references('id')->on('ri_protein')->nullOnDelete();
            $table->foreign('recipe_style_id')->references('id')->on('ri_recipe_style')->nullOnDelete();
        });

        Schema::create('ri_recipe_ingredient', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('recipe_id');
            $table->unsignedInteger('ingredient_id');

            $table->index('recipe_id');
            $table->index('ingredient_id');
            $table->foreign('recipe_id')->references('id')->on('ri_recipe')->cascadeOnDelete();
            $table->foreign('ingredient_id')->references('id')->on('ri_ingredient')->cascadeOnDelete();
        });

        Schema::create('ri_home_inventory', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ingredient_id');

            $table->index('ingredient_id');
            $table->foreign('ingredient_id')->references('id')->on('ri_ingredient')->cascadeOnDelete();
        });

        Schema::create('ri_related_ingredient', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ingredient_id');
            $table->unsignedInteger('related_ingredient_id');

            $table->index('ingredient_id');
            $table->index('related_ingredient_id');
            $table->foreign('ingredient_id')->references('id')->on('ri_ingredient')->cascadeOnDelete();
            $table->foreign('related_ingredient_id')->references('id')->on('ri_ingredient')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ri_related_ingredient');
        Schema::dropIfExists('ri_home_inventory');
        Schema::dropIfExists('ri_recipe_ingredient');
        Schema::dropIfExists('ri_recipe');
        Schema::dropIfExists('ri_ingredient');
        Schema::dropIfExists('ri_recipe_style');
        Schema::dropIfExists('ri_protein');
        Schema::dropIfExists('ri_ingredient_type');
    }
};
