<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeInventory extends RecipeModel
{
    protected $table = 'ri_home_inventory';

    protected $fillable = ['ingredient_id'];

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id');
    }
}
