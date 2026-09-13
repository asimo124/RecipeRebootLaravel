<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'pgsql';

    public function up(): void
    {
        Schema::connection('pgsql')->create('apple_calendar_events', function (Blueprint $table) {
            $table->increments('id');
            $table->text('id_str')->nullable();
            $table->text('title')->nullable();
            $table->text('calendar_name')->nullable();
            $table->text('location')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->smallInteger('all_day')->default(0);
            $table->text('description')->nullable();
            $table->text('url')->nullable();
            $table->smallInteger('to_delete')->default(0);
            $table->index('calendar_name');
            $table->index('start_date');
            $table->index('end_date');
            $table->index('to_delete');
        });

        $pgsql = DB::connection('pgsql');

        $pgsql->statement("
            ALTER TABLE apple_calendar_events
            ADD COLUMN search_vector tsvector
            GENERATED ALWAYS AS (
                setweight(to_tsvector('english', coalesce(title, '')), 'A') ||
                setweight(to_tsvector('english', coalesce(description, '')), 'B') ||
                setweight(to_tsvector('english', coalesce(location, '')), 'C')
            ) STORED
        ");

        $pgsql->statement('CREATE INDEX apple_calendar_events_search_vector_idx ON apple_calendar_events USING GIN (search_vector)');
        $pgsql->statement("CREATE INDEX apple_calendar_events_title_fts_idx ON apple_calendar_events USING GIN (to_tsvector('english', coalesce(title, '')))");
        $pgsql->statement("CREATE INDEX apple_calendar_events_description_fts_idx ON apple_calendar_events USING GIN (to_tsvector('english', coalesce(description, '')))");
        $pgsql->statement("CREATE INDEX apple_calendar_events_location_fts_idx ON apple_calendar_events USING GIN (to_tsvector('english', coalesce(location, '')))");
    }

    public function down(): void
    {
        Schema::connection('pgsql')->dropIfExists('apple_calendar_events');
    }
};
