<?php

namespace App\Support;

use App\Models\RecipeModel;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecipeBookSchema
{
    public static function createCoreTables(): void
    {
        $schema = Schema::connection(RecipeModel::CONNECTION_NAME);

        if (! $schema->hasTable('ri_ingredient_type')) {
            $schema->create('ri_ingredient_type', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
            });
        }

        if (! $schema->hasTable('ri_protein')) {
            $schema->create('ri_protein', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
            });
        }

        if (! $schema->hasTable('ri_recipe_style')) {
            $schema->create('ri_recipe_style', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
            });
        }

        if (! $schema->hasTable('ri_ingredient')) {
            $schema->create('ri_ingredient', function (Blueprint $table) {
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
        }

        if (! $schema->hasTable('ri_recipe')) {
            $schema->create('ri_recipe', function (Blueprint $table) {
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
        }

        if (! $schema->hasTable('ri_recipe_ingredient')) {
            $schema->create('ri_recipe_ingredient', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('recipe_id');
                $table->unsignedInteger('ingredient_id');

                $table->index('recipe_id');
                $table->index('ingredient_id');
                $table->foreign('recipe_id')->references('id')->on('ri_recipe')->cascadeOnDelete();
                $table->foreign('ingredient_id')->references('id')->on('ri_ingredient')->cascadeOnDelete();
            });
        }

        if (! $schema->hasTable('ri_home_inventory')) {
            $schema->create('ri_home_inventory', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('ingredient_id');

                $table->index('ingredient_id');
                $table->foreign('ingredient_id')->references('id')->on('ri_ingredient')->cascadeOnDelete();
            });
        }

        if (! $schema->hasTable('ri_related_ingredient')) {
            $schema->create('ri_related_ingredient', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('ingredient_id');
                $table->unsignedInteger('related_ingredient_id');

                $table->index('ingredient_id');
                $table->index('related_ingredient_id');
                $table->foreign('ingredient_id')->references('id')->on('ri_ingredient')->cascadeOnDelete();
                $table->foreign('related_ingredient_id')->references('id')->on('ri_ingredient')->cascadeOnDelete();
            });
        }
    }

    public static function createAttributeTables(): void
    {
        $schema = Schema::connection(RecipeModel::CONNECTION_NAME);

        if (! $schema->hasTable('ri_attribute')) {
            $schema->create('ri_attribute', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
                $table->integer('severity_level');
            });
        }

        if (! $schema->hasTable('ri_recipe_attribute')) {
            $schema->create('ri_recipe_attribute', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('recipe_id');
                $table->unsignedInteger('attribute_id');

                $table->index('recipe_id');
                $table->index('attribute_id');
                $table->unique(['recipe_id', 'attribute_id']);
                $table->foreign('recipe_id')->references('id')->on('ri_recipe')->cascadeOnDelete();
                $table->foreign('attribute_id')->references('id')->on('ri_attribute')->cascadeOnDelete();
            });
        }
    }

    public static function dropAttributeTables(): void
    {
        $schema = Schema::connection(RecipeModel::CONNECTION_NAME);
        $schema->dropIfExists('ri_recipe_attribute');
        $schema->dropIfExists('ri_attribute');
    }

    public static function dropCoreTables(): void
    {
        $schema = Schema::connection(RecipeModel::CONNECTION_NAME);
        $schema->dropIfExists('ri_related_ingredient');
        $schema->dropIfExists('ri_home_inventory');
        $schema->dropIfExists('ri_recipe_ingredient');
        $schema->dropIfExists('ri_recipe');
        $schema->dropIfExists('ri_ingredient');
        $schema->dropIfExists('ri_recipe_style');
        $schema->dropIfExists('ri_protein');
        $schema->dropIfExists('ri_ingredient_type');
    }
}
