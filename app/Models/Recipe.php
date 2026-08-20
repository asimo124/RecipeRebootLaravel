<?php

namespace App\Models;

use App\Models\Concerns\SoftDeletesByFlag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Recipe extends Model
{
    use SoftDeletesByFlag;

    protected $table = 'ri_recipe';

    public $timestamps = false;

    protected $fillable = [
        'title',
        'is_deleted',
        'last_date_made',
        'contains_gluten',
        'image_path',
        'protein_id',
        'recipe_style_id',
        'recipe_link',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
        'contains_gluten' => 'boolean',
        'last_date_made' => 'date',
    ];

    public function protein(): BelongsTo
    {
        return $this->belongsTo(Protein::class, 'protein_id');
    }

    public function style(): BelongsTo
    {
        return $this->belongsTo(RecipeStyle::class, 'recipe_style_id');
    }

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'ri_recipe_ingredient', 'recipe_id', 'ingredient_id')
            ->withPivot('id');
    }

    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'ri_recipe_attribute', 'recipe_id', 'attribute_id')
            ->withPivot('id');
    }
}
