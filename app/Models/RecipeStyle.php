<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class RecipeStyle extends RecipeModel
{
    protected $table = 'ri_recipe_style';

    protected $fillable = ['title'];

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class, 'recipe_style_id');
    }
}
