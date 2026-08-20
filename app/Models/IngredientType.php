<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IngredientType extends Model
{
    protected $table = 'ri_ingredient_type';

    public $timestamps = false;

    protected $fillable = ['title'];

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class, 'ingredient_type_id');
    }
}
