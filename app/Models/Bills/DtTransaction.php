<?php

namespace App\Models\Bills;

use App\Enums\Bills\TransactionType;

class DtTransaction extends BillsModel
{
    protected $table = 'dt_transaction';

    protected $attributes = [
        'transaction_type' => TransactionType::Disposable->value,
    ];

    protected $casts = [
        'transaction_type' => TransactionType::class,
    ];
}
