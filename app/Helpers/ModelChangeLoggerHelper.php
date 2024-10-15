<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

class ModelChangeLoggerHelper
{
    /**
     * Get change attribute of model
     */
    public static function getModelChanges(Model $model): array
    {
        $originalAttributes = $model->getOriginal();
        $changedAttributes = $model->getDirty();

        $changes = [];
        foreach ($changedAttributes as $attribute => $newValue) {
            $oldValue = $originalAttributes[$attribute] ?? null;
            if ($oldValue !== $newValue) {
                $changes[$attribute] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }
}
