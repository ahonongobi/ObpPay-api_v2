<?php

namespace App\Services;

class FeeService
{
    public static function transferToGSM($amount)
    {
        return $amount + ($amount * config('fees.transfer_to_gsm'));
    }

    public static function withdraw($amount)
    {
        return $amount + ($amount * config('fees.withdraw'));
    }

    public static function transferObpToObp($amount)
    {
        return $amount + config('fees.transfer_obppay_to_obppay');
    }

    public static function weeklyInterest($amount)
    {
        return $amount * config('fees.weekly_interest');
    }

    public static function weeklyPenalty($amount)
    {
        return $amount * config('fees.weekly_penalty');
    }
}
