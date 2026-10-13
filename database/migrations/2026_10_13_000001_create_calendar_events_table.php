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
            if (Schema::connection($connection)->hasTable('calendar_events')) {
                continue;
            }

            Schema::connection($connection)->create('calendar_events', function (Blueprint $table) {
                $table->unsignedBigInteger('id')->autoIncrement();
                $table->char('event_key', 64);
                $table->char('calendar_id', 64);
                $table->string('calendar_name');
                $table->string('uid', 512);
                $table->text('summary')->nullable();
                $table->mediumText('description')->nullable();
                $table->text('location')->nullable();
                $table->string('status', 32)->nullable();
                $table->dateTime('start_utc');
                $table->dateTime('end_utc');
                $table->boolean('all_day')->default(false);
                $table->boolean('is_recurring')->default(false);
                $table->text('rrule')->nullable();
                $table->dateTime('last_modified_utc')->nullable();
                $table->bigInteger('sync_run');
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
                $table->unique('event_key', 'uq_event_key');
                $table->index('start_utc', 'ix_start');
                $table->index(['calendar_id', 'start_utc'], 'ix_cal_start');
            });
        }
    }

    public function down(): void
    {
        // Never drop the live table; the CalDAV cron owns that data.
        Schema::connection('asimo124_bills_test')->dropIfExists('calendar_events');
    }
};
