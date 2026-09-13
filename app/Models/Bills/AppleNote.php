<?php

namespace App\Models\Bills;

use App\Models\RecipeModel;
use Illuminate\Database\Eloquent\Builder;

class AppleNote extends RecipeModel
{
    protected $table = 'apple_notes';

    protected $fillable = [
        'id_str',
        'name',
        'folder',
        'account',
        'creation_date',
        'modification_date',
        'body',
        'to_delete',
        'has_duplicates',
    ];

    protected $hidden = [
        'search_vector',
    ];

    protected $casts = [
        'to_delete' => 'integer',
        'has_duplicates' => 'integer',
        'creation_date' => 'datetime',
        'modification_date' => 'datetime',
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
        return $this->scopeTsvectorColumn($query, 'name', $keyword);
    }

    public function scopeSearchBody(Builder $query, ?string $keyword): Builder
    {
        return $this->scopeTsvectorColumn($query, 'body', $keyword);
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

        $allowed = ['name', 'body'];
        if (! in_array($column, $allowed, true)) {
            return $query;
        }

        return $query->whereRaw(
            "to_tsvector('english', coalesce({$column}, '')) @@ to_tsquery('english', ?)",
            [$tsQuery]
        );
    }
}
