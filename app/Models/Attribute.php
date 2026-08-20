<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Attribute extends Model
{
    protected $table = 'ri_attribute';

    public $timestamps = false;

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
