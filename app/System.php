<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class System extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Retrieves a system value from the given key.
     *
     * @param string $key
     */
    public static function getValue($key, $json_decode = null)
    {
        $value = System::where('key', $key)->value('value');

        if (!empty($json_decode)) {
            $value = json_decode($value, true);
        }
        
        return $value;
    }

    /**
     * Retrieves a business value from the given key.
     *
     * @param string $key
     */
    public static function getSystemSettings($keys)
    {
        $system = System::whereIn('key', $keys)
                    ->pluck('value', 'key');

        return $system;
    }
    /**
     * Returns the date formats
     */
    public static function date_formats()
    {
        return [
                ['text' => 'dd-mm-yyyy', 'value' => 'd-m-Y'],
                ['text' => 'mm-dd-yyyy', 'value' => 'm-d-Y'],
                ['text' => 'dd/mm/yyyy', 'value' => 'd/m/Y'],
                ['text' => 'mm/dd/yyyy', 'value' => 'm/d/Y'],
            ];
    }

    /**
     * Returns the time formats
     */
    public static function time_formats()
    {
        return [
                ['text' => __('messages.12_hour'), 'value' => '12'],
                ['text' => __('messages.24_hour'), 'value' => '24'],
            ];
    }

    /**
     * Returns business currency
     */
    public static function getBusinessCurrency($key)
    {
        $value = System::where('key', $key)->value('value');

        $currency = Currency::find($value);

        return $currency;
    }
}
