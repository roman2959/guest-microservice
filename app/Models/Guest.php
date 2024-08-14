<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use libphonenumber\PhoneNumberUtil;

class Guest extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'country'
    ];

    // Автоматически определяем страну по номеру телефона, если она не указана
    public static function boot()
    {
        parent::boot();

        static::creating(function ($guest) {
            if (empty($guest->country)) {
                $guest->country = self::determineCountryFromPhone($guest->phone);
            }
        });
    }

    // Функция для определения страны по телефону
    public static function determineCountryFromPhone($phone)
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        $phoneNumber = $phoneNumberUtil->parse($phone, null);
        $regionCode = $phoneNumberUtil->getRegionCodeForNumber($phoneNumber);

        return $regionCode;
    }
}
