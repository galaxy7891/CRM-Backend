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
        // $changes = ModelChangeLoggerHelper::getModelChanges($product);

        // ActivityLog::create([
        //     'user_id' => auth()->id(),
        //     'model_name' => 'products',
        //     'action' => 'CREATE',
        //     'changes' => $changes ? json_encode($changes) : null,
        // ]);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($product);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'model_name' => 'products',
            'action' => 'UPDATE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $changes = ModelChangeLoggerHelper::getModelChanges($product);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'model_name' => 'products',
            'action' => 'DELETE',
            'changes' => $changes ? json_encode($changes) : null,
        ]);
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        //
    }
}
