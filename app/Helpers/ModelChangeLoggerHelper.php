<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

class ModelChangeLoggerHelper
{
    /**
     * Get change attribute of model
     */
    public static function getModelChanges(Model $model, ?string $modelName): array
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

        if (isset($originalAttributes['id'])) {
            $changes['id'] = [
                'old' => $originalAttributes['id'],  
                'new' => $model->getKey(),
            ];
        }

        if ($modelName === 'customers' && isset($originalAttributes['customerCategory'])) {
            $changes['customerCategory'] = [
                'old' => $originalAttributes['customerCategory'],
                'new' => $model->customerCategory,
            ];
        }

        return $changes;
    }
}
