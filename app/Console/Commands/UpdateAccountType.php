<?php

namespace App\Console\Commands;

use App\Models\AccountsType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateAccountType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:account-type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perbarui tipe akun pelanggan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->toDateString();

        $accountsType = AccountsType::where('end_date', $today)
            ->where('account_type', '!=', 'unactive')
            ->update([
                'account_type' => 'unactive',
                'start_date' => null,
                'end_date' => null
            ]);
            
        Log::info("Scheduler executed: {$accountsType} accounts updated.");
        $this->info($accountsType. ' akun pelanggan menjadi tidak aktif');
    }
}
