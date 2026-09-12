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
            if (Schema::connection($connection)->hasTable('dl_hemmerhoid_log')) {
                continue;
            }

            Schema::connection($connection)->create('dl_hemmerhoid_log', function (Blueprint $table) {
                $table->increments('id');
                $table->dateTime('date_pooped');
                $table->unsignedTinyInteger('pain_level');
                $table->unsignedTinyInteger('blood_level');
                $table->index('date_pooped');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->connections as $connection) {
            Schema::connection($connection)->dropIfExists('dl_hemmerhoid_log');
        }
    }
};
