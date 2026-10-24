<?php

namespace App\Models\Bills;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EpPurchase extends BillsModel
{
    protected $table = 'ep_purchase';

    protected $casts = [
        'pay_period_id' => 'integer',
        'amount' => 'float',
        'purchase_date' => 'date',
    ];

    public function payPeriod(): BelongsTo
    {
        return $this->belongsTo(IpPayPeriod::class, 'pay_period_id');
    }
}
