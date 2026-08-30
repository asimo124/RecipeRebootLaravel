<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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

            if (Schema::connection($connection)->hasColumn('dt_transaction', 'transaction_type')) {
                continue;
            }

            Schema::connection($connection)->table('dt_transaction', function (Blueprint $table) {
                $table->enum('transaction_type', ['disposable', 'covered', 'impulse buy'])
                    ->default('disposable')
                    ->after('is_covered');
            });
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

            Schema::connection($connection)->table('dt_transaction', function (Blueprint $table) {
                $table->dropColumn('transaction_type');
            });
        }
    }
};
