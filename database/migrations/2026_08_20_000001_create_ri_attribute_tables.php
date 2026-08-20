<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ri_attribute', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->integer('severity_level');
        });

        Schema::create('ri_recipe_attribute', function (Blueprint $table) {
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

    public function down(): void
    {
        Schema::dropIfExists('ri_recipe_attribute');
        Schema::dropIfExists('ri_attribute');
    }
};
