<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Attribute extends RecipeModel
{
    protected $table = 'ri_attribute';

    protected $fillable = [
        'title',
        'severity_level',
    ];

    protected $casts = [
        'severity_level' => 'integer',
    ];

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'ri_recipe_attribute', 'attribute_id', 'recipe_id')
            ->withPivot('id');
    }
}
