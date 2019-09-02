<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoiceLine extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get the transaction that owns the invoice lines.
     */
    public function transaction()
    {
        return $this->belongsTo('App\Transaction');
    }
}
