<?php

namespace App\Models\Bills;

use App\Enums\Bills\TransactionType;

class DtTransactionTypeSavedSearch extends BillsModel
{
    protected $table = 'dt_transaction_type_saved_search';

    protected $attributes = [
        'transaction_type' => TransactionType::Covered->value,
    ];

    protected $casts = [
        'transaction_type' => TransactionType::class,
    ];
}
