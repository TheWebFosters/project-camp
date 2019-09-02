<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends Model
{
    use LogsActivity;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    protected static $logUnguarded = true;
    protected static $logOnlyDirty = true;

    /**
    * Scope a query to only include transaction of a given type.
    *
    * @param  \Illuminate\Database\Eloquent\Builder  $query
    * @param  mixed  $type
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function scopeOfTransaction($query, $type)
    {
        return $query->where('transactions.type', $type);
    }

    /**
     * Get the invoice lines for the transaction.
     */
    public function invoiceLines()
    {
        return $this->hasMany('App\InvoiceLine');
    }

    /**
     * Get the customer that owns the transaction.
     */
    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

    /**
     * Get the project for the transaction.
     */
    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    /**
     * Get the payments for the transaction.
     */
    public function payments()
    {
        return $this->hasMany('App\TransactionPaymentLine');
    }

    /**
     * Return the expense for
     */
    public function expenseFor()
    {
        return $this->belongsTo('App\Components\User\Models\User', 'expense_for');
    }

    /**
     * Get download link.
     */
    public function getDownloadUrlAttribute()
    {
        return action('InvoiceController@download', $this->id);
    }

    /**
     * Get download link.
     */
    public function getInvoiceNameAttribute()
    {
        if ('estimate' == $this->status) {
            $invoice_name = 'Estimate-'.$this->ref_no.'.pdf';
        } elseif ('final' == $this->status) {
            $invoice_name = 'Invoice-'.$this->ref_no.'.pdf';
        } elseif ('draft' == $this->status) {
            $invoice_name = 'Draft-'.$this->ref_no.'.pdf';
        }

        return $invoice_name;
    }

    /**
     * Return the discount_type for the invoice.
     */
    public static function getDiscountType()
    {
        $discount_type = [
                            ['key' => 'fixed',
                             'value' => __('messages.fixed'),
                            ],
                            ['key' => 'percentage',
                             'value' => __('messages.percentage'),
                            ],
                        ];

        return $discount_type;
    }

    /**
     * Return the payment_statuses for the invoice.
     */
    public static function paymentStatuses($append_all = false)
    {
        $payment_statuses = [
            ['value' => 'paid', 'label' => __('messages.paid')],
            ['value' => 'partial', 'label' => __('messages.partial')],
            ['value' => 'due', 'label' => __('messages.due')],
        ];

        if ($append_all) {
            $payment_statuses = array_merge([['value' => '', 'label' => __('messages.all')]], $payment_statuses);
        }

        return $payment_statuses;
    }

    /**
     * Return the type for the invoice.
     */
    public static function getInvoiceType()
    {
        $invoice_type = [
                        ['text' => __('messages.final'), 'value' => 'final'],
                        ['text' => __('messages.estimate'), 'value' => 'estimate'],
                        ['text' => __('messages.draft'), 'value' => 'draft'],
                ];

        return $invoice_type;
    }
}
