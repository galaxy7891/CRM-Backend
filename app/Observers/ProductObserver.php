<?php

namespace App\Observers;

use App\Helpers\ModelChangeLoggerHelper;
use App\Models\ActivityLog;
use App\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($product, 'products', 'CREATE');

        $activityLog = new ActivityLog();
        $activityLog->user_id = auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100';
        $activityLog->model_name = 'products';
        $activityLog->action = 'CREATE';
        $activityLog->changes = $changes ? json_encode($changes) : null;
        $activityLog->save();
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($product, 'products', 'UPDATE');

        $activityLog = new ActivityLog();
        $activityLog->user_id = auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100';
        $activityLog->model_name = 'products';
        $activityLog->action = 'UPDATE';
        $activityLog->changes = $changes ? json_encode($changes) : null;
        $activityLog->save();
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($product, 'products', 'DELETE');

        $product->update([
            'name' => time() . '::' . $product->name,
            'code' => time() . '::' . $product->code,
        ]);

        $activityLog = new ActivityLog();
        $activityLog->user_id = auth()->id() ? auth()->id() : '123e4567-e89b-12d3-a456-426614174100';
        $activityLog->model_name = 'products';
        $activityLog->action = 'DELETE';
        $activityLog->changes = $changes ? json_encode($changes) : null;
        $activityLog->save();
    }
}
