<?php

namespace App\Observers;

use App\Models\Logger;
use App\Models\Customer;
use Illuminate\Support\Str;

class CustomerObserver
{
    /**
     * Handle the Customer "created" event.
     */
    public function created(Customer $customer): void
    {
        // Logger::create([
        //     'id' => Str::uuid(),
        //     'user_id' => auth()->id(),
        //     'table_name' => 'customers',
        //     'action' => 'CREATE',
        //     'description' => "Customer {$customer->first_name} {$customer->last_name} telah ditambahkan.",
        // ]);
    }

    /**
     * Handle the Customer "updated" event.
     */
    public function updated(Customer $customer): void
    {
        Logger::create([
            'id' => Str::uuid(),
            'user_id' => auth()->id(),
            'table_name' => 'customers',
            'action' => 'UPDATE',
            'description' => "Customer {$customer->first_name} {$customer->last_name} telah diubah.",
        ]);
    }

    /**
     * Handle the Customer "deleted" event.
     */
    public function deleted(Customer $customer): void
    {
        Logger::create([
            'id' => Str::uuid(),
            'user_id' => auth()->id(),
            'table_name' => 'customers',
            'action' => 'DELETE',
            'description' => "Customer {$customer->first_name} {$customer->last_name} telah dihapus.",
        ]);
    }

    /**
     * Handle the Customer "restored" event.
     */
    public function restored(Customer $customer): void
    {
        //
    }

    /**
     * Handle the Customer "force deleted" event.
     */
    public function forceDeleted(Customer $customer): void
    {
        //
    }
}
