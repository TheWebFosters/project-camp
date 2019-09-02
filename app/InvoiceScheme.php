<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceScheme extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * retrieve data for dropdown
     *
     * @return $invoice_schemes
     */
    public static function forDropDown()
    {
        $invoice_schemes = InvoiceScheme::select('id', 'name')
                            ->orderBy('name')
                            ->get()
                            ->toArray();

        return $invoice_schemes;
    }

    /**
     * retrieve the default invoice scheme
     *
     * @return $invoice_scheme
     */
    public static function getDefault()
    {
        $invoice_scheme = InvoiceScheme::where('is_default', 1)
                            ->first();

        return $invoice_scheme;
    }
}
