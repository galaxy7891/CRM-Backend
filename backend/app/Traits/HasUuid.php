<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    /**
     * Boot the trait.
     */
    protected static function bootHasUuid()
    {
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the incrementing property.
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the key type property.
     */
    public function getKeyType()
    {
        return 'string';
    }
}
