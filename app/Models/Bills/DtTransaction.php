<?php

namespace App\Models\Bills;

use App\Enums\Bills\TransactionType;

class DtTransaction extends BillsModel
{
    protected $table = 'dt_transaction';

    protected $casts = [
        'transaction_type' => TransactionType::class,
    ];
}
