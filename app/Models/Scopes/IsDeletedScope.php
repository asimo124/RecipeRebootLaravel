<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class IsDeletedScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $column = method_exists($model, 'getIsDeletedColumn')
            ? $model->getIsDeletedColumn()
            : 'is_deleted';

        $builder->where($model->getTable().'.'.$column, 0);
    }
}
