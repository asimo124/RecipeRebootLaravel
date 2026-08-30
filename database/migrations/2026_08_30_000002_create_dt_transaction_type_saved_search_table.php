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
            if (Schema::connection($connection)->hasTable('dt_transaction_type_saved_search')) {
                continue;
            }

            Schema::connection($connection)->create('dt_transaction_type_saved_search', function (Blueprint $table) {
                $table->increments('id');
                $table->string('keyword');
                $table->string('search_type');
                $table->enum('transaction_type', ['disposable', 'covered', 'impulse buy'])
                    ->default('covered');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->connections as $connection) {
            Schema::connection($connection)->dropIfExists('dt_transaction_type_saved_search');
        }
    }
};
