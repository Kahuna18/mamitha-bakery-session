<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function setValue($key, $value)
    {
        return self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function isOpen()
    {
        $isClosed = self::getValue('is_closed', 'false');
        return $isClosed !== 'true';
    }

    public static function getDailyOrderLimit()
    {
        return (int) self::getValue('daily_order_limit', 0);
    }

    public static function getTodayOrderCount()
    {
        return \App\Models\Order::whereDate('created_at', today())->count();
    }

    public static function canAcceptOrder()
    {
        $limit = self::getDailyOrderLimit();
        if ($limit <= 0) return self::isOpen();
        return self::isOpen() && self::getTodayOrderCount() < $limit;
    }
}
