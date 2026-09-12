<?php

namespace App\Models\Bills;

class DlHemmerhoidLog extends BillsModel
{
    protected $table = 'dl_hemmerhoid_log';

    protected $casts = [
        'date_pooped' => 'datetime',
        'pain_level' => 'integer',
        'blood_level' => 'integer',
    ];
}
