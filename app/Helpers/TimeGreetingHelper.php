<?php

namespace App\Helpers;

use Carbon\Carbon;

class TimeGreetingHelper
{
    public static function getGreeting(): string
    {
        $currentHour = Carbon::now()->format('H'); 

        switch (true) {
            case ($currentHour >= 3 && $currentHour < 12):
                return 'Selamat pagi';
            case ($currentHour >= 12 && $currentHour < 15):
                return 'Selamat siang';
            case ($currentHour >= 15 && $currentHour < 18):
                return 'Selamat sore';
            default:
                return 'Selamat malam';
        }        
    }
}
