<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Protein extends Model
{
    protected $table = 'ri_protein';

    public $timestamps = false;

    protected $fillable = ['title'];

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class, 'protein_id');
    }
}
