<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecipeStyle extends Model
{
    protected $table = 'ri_recipe_style';

    public $timestamps = false;

    protected $fillable = ['title'];

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class, 'recipe_style_id');
    }
}
