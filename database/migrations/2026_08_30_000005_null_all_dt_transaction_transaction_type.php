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
            if (! Schema::connection($connection)->hasTable('dt_transaction')) {
                continue;
            }

            if (! Schema::connection($connection)->hasColumn('dt_transaction', 'transaction_type')) {
                continue;
            }

            DB::connection($connection)->table('dt_transaction')->update([
                'transaction_type' => null,
            ]);
        }
    }

    public function down(): void
    {
        foreach ($this->connections as $connection) {
            if (! Schema::connection($connection)->hasTable('dt_transaction')) {
                continue;
            }

            if (! Schema::connection($connection)->hasColumn('dt_transaction', 'transaction_type')) {
                continue;
            }

            DB::connection($connection)->table('dt_transaction')
                ->whereNull('transaction_type')
                ->update(['transaction_type' => 'disposable']);
        }
    }
};
