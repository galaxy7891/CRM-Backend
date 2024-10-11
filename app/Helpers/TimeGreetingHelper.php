<?php

namespace App\Helpers;

use Carbon\Carbon;

class TimeGreetingHelper
{
    public static function getGreeting(): string
    {
        $currentHour = Carbon::now()->format('H'); 

        switch (true) {
            case ($currentHour >= 5 && $currentHour < 12):
                return 'Selamat Pagi';
            case ($currentHour >= 12 && $currentHour < 15):
                return 'Selamat Siang';
            case ($currentHour >= 15 && $currentHour < 18):
                return 'Selamat Sore';
            default:
                return 'Selamat Malam';
        }        
    }
}
