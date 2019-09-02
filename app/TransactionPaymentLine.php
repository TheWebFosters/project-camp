<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionPaymentLine extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the payment_line that owns the invoice.
     */
    public function transaction()
    {
        return $this->belongsTo('App\Transaction');
    }
}
