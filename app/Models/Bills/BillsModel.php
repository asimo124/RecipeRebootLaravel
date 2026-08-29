<?php

namespace App\Models\Bills;

use Illuminate\Database\Eloquent\Model;

abstract class BillsModel extends Model
{
    protected $connection = 'asimo124_bills';

    public $timestamps = false;

    protected $guarded = [];
}
