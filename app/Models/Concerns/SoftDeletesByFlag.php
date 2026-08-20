<?php

namespace App\Models\Concerns;

use App\Models\Scopes\IsDeletedScope;

trait SoftDeletesByFlag
{
    public static function bootSoftDeletesByFlag(): void
    {
        static::addGlobalScope(new IsDeletedScope);

        static::deleting(function ($model) {
            if ($model->isForceDeleting()) {
                return;
            }

            $model->{$model->getIsDeletedColumn()} = 1;
            $model->save();

            return false;
        });
    }

    public function initializeSoftDeletesByFlag(): void
    {
        if (! isset($this->casts[$this->getIsDeletedColumn()])) {
            $this->casts[$this->getIsDeletedColumn()] = 'boolean';
        }
    }

    public function getIsDeletedColumn(): string
    {
        return 'is_deleted';
    }

    public function isForceDeleting(): bool
    {
        return property_exists($this, 'forceDeleting') && $this->forceDeleting === true;
    }

    public function forceDelete()
    {
        $this->forceDeleting = true;

        return $this->delete();
    }

    public function restore(): bool
    {
        $this->{$this->getIsDeletedColumn()} = 0;

        return $this->save();
    }

    public function trashed(): bool
    {
        return (bool) $this->{$this->getIsDeletedColumn()};
    }
}
