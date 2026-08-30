<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var list<string> */
    private array $connections = ['asimo124_bills', 'asimo124_bills_test'];

    public function up(): void
    {
        foreach ($this->connections as $connection) {
            if (! Schema::connection($connection)->hasTable('dt_transaction_type_saved_search')) {
                continue;
            }

            DB::connection($connection)->statement(
                "ALTER TABLE dt_transaction_type_saved_search
                 MODIFY COLUMN search_type ENUM('contains', 'starts with', 'ends with', 'regex') NOT NULL"
            );
        }
    }

    public function down(): void
    {
        foreach ($this->connections as $connection) {
            if (! Schema::connection($connection)->hasTable('dt_transaction_type_saved_search')) {
                continue;
            }

            DB::connection($connection)->statement(
                'ALTER TABLE dt_transaction_type_saved_search MODIFY COLUMN search_type VARCHAR(255) NOT NULL'
            );
        }
    }
};
