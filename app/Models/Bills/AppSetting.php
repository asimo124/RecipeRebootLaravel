<?php

namespace App\Models\Bills;

class AppSetting extends BillsModel
{
    protected $table = 'app_settings';

    protected $primaryKey = 'setting_key';

    public $incrementing = false;

    protected $keyType = 'string';
}
