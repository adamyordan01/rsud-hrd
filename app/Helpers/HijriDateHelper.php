<?php
namespace App\Helpers;

use Remls\HijriDate\HijriDate;


class HijriDateHelper
{
    private static $customHijriMonths = [
        1 => "Muharram", 
        "Safar", 
        "Rabi'ul Awwal", 
        "Rabi'ul Akhir", 
        "Jumadil Awal", 
        "Jumadil Akhir",
        "Rajab", 
        "Sya'ban", 
        "Ramadhan", 
        "Syawal", 
        "Dzulqo'dah", 
        "Dzulhijjah"
    ];

    public static function formatHijriDate($date)
    {
        $hijriDate = HijriDate::createFromGregorian($date);
        $day = sprintf('%02d', $hijriDate->day);
        // $month = $hijriDate->format('F');
        $month = self::$customHijriMonths[$hijriDate->month];
        $year = $hijriDate->year;

        return "{$day} {$month} {$year} H";
    }
}