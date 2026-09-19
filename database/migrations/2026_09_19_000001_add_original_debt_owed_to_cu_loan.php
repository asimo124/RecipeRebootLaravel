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
            if (! Schema::connection($connection)->hasTable('cu_loan')) {
                continue;
            }

            if (Schema::connection($connection)->hasColumn('cu_loan', 'original_debt_owed')) {
                continue;
            }

            Schema::connection($connection)->table('cu_loan', function (Blueprint $table) {
                $table->decimal('original_debt_owed', 10, 2)->default(0)->after('debt_owed');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->connections as $connection) {
            if (! Schema::connection($connection)->hasTable('cu_loan')) {
                continue;
            }

            if (! Schema::connection($connection)->hasColumn('cu_loan', 'original_debt_owed')) {
                continue;
            }

            Schema::connection($connection)->table('cu_loan', function (Blueprint $table) {
                $table->dropColumn('original_debt_owed');
            });
        }
    }
};
