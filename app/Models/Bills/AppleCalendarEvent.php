<?php

namespace App\Models\Bills;

use App\Models\RecipeModel;
use Illuminate\Database\Eloquent\Builder;

class AppleCalendarEvent extends RecipeModel
{
    protected $table = 'apple_calendar_events';

    protected $fillable = [
        'id_str',
        'title',
        'calendar_name',
        'location',
        'start_date',
        'end_date',
        'all_day',
        'description',
        'url',
        'to_delete',
    ];

    protected $hidden = [
        'search_vector',
    ];

    protected $casts = [
        'all_day' => 'integer',
        'to_delete' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public static function buildTsQuery(?string $keyword): ?string
    {
        if ($keyword === null) {
            return null;
        }

        preg_match_all('/[A-Za-z0-9]+/', $keyword, $matches);
        if ($matches[0] === []) {
            return null;
        }

        return implode(' & ', array_map(
            static fn (string $word): string => $word.':*',
            $matches[0]
        ));
    }

    public function scopeSearchTitle(Builder $query, ?string $keyword): Builder
    {
        return $this->scopeTsvectorColumn($query, 'title', $keyword);
    }

    public function scopeSearchDescription(Builder $query, ?string $keyword): Builder
    {
        return $this->scopeTsvectorColumn($query, 'description', $keyword);
    }

    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        $tsQuery = self::buildTsQuery($keyword);
        if ($tsQuery === null) {
            return $query;
        }

        return $query->whereRaw('search_vector @@ to_tsquery(\'english\', ?)', [$tsQuery]);
    }

    private function scopeTsvectorColumn(Builder $query, string $column, ?string $keyword): Builder
    {
        $tsQuery = self::buildTsQuery($keyword);
        if ($tsQuery === null) {
            return $query;
        }

        $allowed = ['title', 'description', 'location'];
        if (! in_array($column, $allowed, true)) {
            return $query;
        }

        return $query->whereRaw(
            "to_tsvector('english', coalesce({$column}, '')) @@ to_tsquery('english', ?)",
            [$tsQuery]
        );
    }
}
