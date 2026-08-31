<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ingredient extends RecipeModel
{
    protected $table = 'ri_ingredient';

    protected $fillable = [
        'title',
        'ingredient_type_id',
        'price',
        'cheap_price',
        'store_section_id',
    ];

    protected $casts = [
        'price' => 'float',
        'cheap_price' => 'float',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(IngredientType::class, 'ingredient_type_id');
    }

    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'ri_recipe_ingredient', 'ingredient_id', 'recipe_id')
            ->withPivot('id');
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(HomeInventory::class, 'ingredient_id');
    }

    /** Specific → general parents (related_ingredient_id is the parent). */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(
            Ingredient::class,
            'ri_related_ingredient',
            'ingredient_id',
            'related_ingredient_id'
        )->withPivot('id');
    }

    /** General → specific children. */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(
            Ingredient::class,
            'ri_related_ingredient',
            'related_ingredient_id',
            'ingredient_id'
        )->withPivot('id');
    }
}
