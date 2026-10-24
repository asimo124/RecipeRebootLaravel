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
            if (Schema::connection($connection)->hasTable('ep_purchase')) {
                continue;
            }

            // pay_period_id references ip_pay_period.id without a DB-level constraint:
            // IpPayPeriod::updatePayPeriods() prunes pay periods outside its rolling
            // window, which a constraint would either block or cascade purchases away.
            Schema::connection($connection)->create('ep_purchase', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('pay_period_id');
                $table->string('title');
                $table->float('amount')->default(0);
                $table->date('purchase_date');
                $table->index('pay_period_id');
                $table->index('purchase_date');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->connections as $connection) {
            Schema::connection($connection)->dropIfExists('ep_purchase');
        }
    }
};
