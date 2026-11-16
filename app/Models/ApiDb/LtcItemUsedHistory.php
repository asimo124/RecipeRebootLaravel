<?php

namespace App\Models\ApiDb;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** One "I did this on <date>" entry for an LtcItem. */
class LtcItemUsedHistory extends Model
{
    protected $connection = 'api_db';

    protected $table = 'ltc_item_used_history';

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'item_id' => 'integer',
        'date_used' => 'date:Y-m-d',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(LtcItem::class, 'item_id');
    }
}
