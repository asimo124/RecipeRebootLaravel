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
        Schema::connection('pgsql')->create('apple_notes', function (Blueprint $table) {
            $table->increments('id');
            $table->text('id_str')->nullable();
            $table->text('name')->nullable();
            $table->text('folder')->nullable();
            $table->text('account')->nullable();
            $table->timestamp('creation_date')->nullable();
            $table->timestamp('modification_date')->nullable();
            $table->text('body')->nullable();
            $table->smallInteger('to_delete')->default(0);
            $table->smallInteger('has_duplicates')->default(0);
            $table->index('folder');
            $table->index('modification_date');
            $table->index('to_delete');
        });

        $pgsql = DB::connection('pgsql');

        $pgsql->statement("
            ALTER TABLE apple_notes
            ADD COLUMN search_vector tsvector
            GENERATED ALWAYS AS (
                setweight(to_tsvector('english', coalesce(name, '')), 'A') ||
                setweight(to_tsvector('english', coalesce(body, '')), 'B')
            ) STORED
        ");

        $pgsql->statement('CREATE INDEX apple_notes_search_vector_idx ON apple_notes USING GIN (search_vector)');
        $pgsql->statement("CREATE INDEX apple_notes_name_fts_idx ON apple_notes USING GIN (to_tsvector('english', coalesce(name, '')))");
        $pgsql->statement("CREATE INDEX apple_notes_body_fts_idx ON apple_notes USING GIN (to_tsvector('english', coalesce(body, '')))");

        $this->copyFromMysql();
    }

    public function down(): void
    {
        Schema::connection('pgsql')->dropIfExists('apple_notes');
    }

    private function copyFromMysql(): void
    {
        if (! Schema::connection('asimo124_bills')->hasTable('apple_notes')) {
            return;
        }

        $hasDuplicates = Schema::connection('asimo124_bills')->hasColumn('apple_notes', 'has_duplicates');
        $pgsql = DB::connection('pgsql');

        DB::connection('asimo124_bills')
            ->table('apple_notes')
            ->orderBy('id')
            ->chunkById(500, function ($rows) use ($pgsql, $hasDuplicates) {
                $payload = [];
                foreach ($rows as $row) {
                    $payload[] = [
                        'id' => $row->id,
                        'id_str' => $row->id_str,
                        'name' => $row->name,
                        'folder' => $row->folder,
                        'account' => $row->account,
                        'creation_date' => $this->nullableTimestamp($row->creation_date ?? null),
                        'modification_date' => $this->nullableTimestamp($row->modification_date ?? null),
                        'body' => $row->body,
                        'to_delete' => (int) ($row->to_delete ?? 0),
                        'has_duplicates' => $hasDuplicates ? (int) ($row->has_duplicates ?? 0) : 0,
                    ];
                }
                if ($payload !== []) {
                    $pgsql->table('apple_notes')->insert($payload);
                }
            });

        $max = $pgsql->table('apple_notes')->max('id');
        if ($max) {
            $pgsql->statement(
                "SELECT setval(pg_get_serial_sequence('apple_notes', 'id'), ?, true)",
                [$max]
            );
        }
    }

    private function nullableTimestamp(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string) $value);
        if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return null;
        }

        return $value;
    }
};
