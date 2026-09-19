<?php

namespace App\Models\Bills;

class CuLoan extends BillsModel
{
    protected $table = 'cu_loan';

    protected $fillable = [
        'title',
        'debt_owed',
        'original_debt_owed',
        'credit_limit',
        'sort_order',
        'milestone_order',
        'min_payment',
        'amount_to_principal',
        'bill_id',
        'can_update_end_date',
        'adjust_disposable_amount',
    ];

    protected $casts = [
        'debt_owed' => 'float',
        'original_debt_owed' => 'float',
        'credit_limit' => 'float',
        'sort_order' => 'integer',
        'milestone_order' => 'integer',
        'min_payment' => 'float',
        'amount_to_principal' => 'float',
        'bill_id' => 'integer',
        'can_update_end_date' => 'boolean',
        'adjust_disposable_amount' => 'float',
    ];
}
