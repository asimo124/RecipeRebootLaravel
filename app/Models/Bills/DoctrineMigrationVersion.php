<?php

namespace App\Models\Bills;

class DoctrineMigrationVersion extends BillsModel
{
    protected $table = 'doctrine_migration_versions';

    protected $primaryKey = 'version';

    public $incrementing = false;

    protected $keyType = 'string';
}
