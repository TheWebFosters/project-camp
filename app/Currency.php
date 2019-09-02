<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
    * Get the Customers for the currency.
    */
    public function customers()
    {
        return $this->hasMany('App\Customer');
    }
}
