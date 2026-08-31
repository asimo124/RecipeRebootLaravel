<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class RecipeModel extends Model
{
    public const CONNECTION_NAME = 'pgsql';

    /**
     * Parent tables first so copies/truncates can respect foreign keys.
     *
     * @var list<string>
     */
    public const TABLES_IN_FK_ORDER = [
        'ri_ingredient_type',
        'ri_protein',
        'ri_recipe_style',
        'ri_attribute',
        'ri_ingredient',
        'ri_recipe',
        'ri_recipe_ingredient',
        'ri_recipe_attribute',
        'ri_home_inventory',
        'ri_related_ingredient',
    ];

    protected $connection = self::CONNECTION_NAME;

    public $timestamps = false;

    public static function existsRule(string $table, string $column = 'id'): string
    {
        return 'exists:'.self::CONNECTION_NAME.'.'.$table.','.$column;
    }
}
