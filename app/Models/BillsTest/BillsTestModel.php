<?php

namespace App\Models\BillsTest;

use Illuminate\Database\Eloquent\Model;

abstract class BillsTestModel extends Model
{
    protected $connection = 'asimo124_bills_test';

    public $timestamps = false;

    protected $guarded = [];
}
