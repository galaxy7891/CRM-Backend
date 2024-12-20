<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Model;

class ModelChangeLoggerHelper
{
    /**
     * Get change attribute of model
     */
    public static function getModelChanges(Model $model, ?string $modelName, string $action): array
    {   
        $originalAttributes = $model->getOriginal();
        $changes = [];
        
        if ($action === 'DELETE') {
            foreach ($originalAttributes as $attribute => $oldValue) {
                $changes[$attribute] = [
                    'old' => $oldValue,
                    'new' => null,
                ];
            }
        
        } else {
            if ($action === 'UPDATE'){
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

                if ($modelName === 'deals' && isset($originalAttributes['stage'])) {
                    $changes['value_estimated'] = [
                        'old' => $originalAttributes['value_estimated'],
                        'new' => $model->value_estimated,
                    ];
                }
            }

            $changedAttributes = $model->getDirty();
            foreach ($changedAttributes as $attribute => $newValue) {
                $oldValue = $originalAttributes[$attribute] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[$attribute] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
        }   
        
        return $changes;
    }
}
