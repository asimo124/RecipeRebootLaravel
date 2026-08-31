<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class IngredientType extends RecipeModel
{
    protected $table = 'ri_ingredient_type';

    protected $fillable = ['title'];

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class, 'ingredient_type_id');
    }
}
