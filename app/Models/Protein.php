<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Protein extends RecipeModel
{
    protected $table = 'ri_protein';

    protected $fillable = ['title'];

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class, 'protein_id');
    }
}
