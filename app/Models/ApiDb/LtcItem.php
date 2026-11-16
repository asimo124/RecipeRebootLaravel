<?php

namespace App\Models\ApiDb;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** "Last Time I" trackable item (managed in api.hawleywebdesign.com's Items CRUD). */
class LtcItem extends Model
{
    protected $connection = 'api_db';

    protected $table = 'ltc_item';

    public $timestamps = false;

    protected $guarded = [];

    public function usedHistory(): HasMany
    {
        return $this->hasMany(LtcItemUsedHistory::class, 'item_id');
    }
}
