<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeInventory extends Model
{
    protected $table = 'ri_home_inventory';

    public $timestamps = false;

    protected $fillable = ['ingredient_id'];

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id');
    }
}
