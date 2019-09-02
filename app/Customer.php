<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class Customer extends Model
{
    use SoftDeletes;
    use LogsActivity;
    use HasRoles;
    use Notifiable;

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = ['id'];
    protected static $logUnguarded = true;
    protected static $logOnlyDirty = true;

    /**
    * The constant for customer status_id
    *
    * @var integer
    */
    public static $STATUS_ID_FOR_CUSTOMER = 1;

    /**
     * Get the contacts for the customer.
     */
    public function contacts()
    {
        return $this->hasMany('App\Components\User\Models\User')
            ->whereNotNull('customer_id');
    }

    /**
     * Get the projects for the customer.
     */
    public function projects()
    {
        return $this->hasMany('App\Project');
    }

    /**
     * Get the transactions for the customer.
     */
    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }

    /**
     * Get all of the customer's notes.
     */
    public function notes()
    {
        return $this->morphMany('App\Note', 'notable');
    }

    /**
     * Get all of the customer's reminders.
     */
    public function reminders()
    {
        return $this->morphMany('App\Reminder', 'remindable');
    }

    /**
     * Get the currency associated with the customer.
     */
    public function currency()
    {
        return $this->belongsTo('App\Currency');
    }

    /**
     * retrieve customers for dropdown
     *
     * @return $customers
     */
    public static function getCustomersForDropDown($append_all = false)
    {
        $customers = Customer::where('status_id', 1)
                            ->select('id', 'company')
                            ->orderBy('company')
                            ->get()
                            ->toArray();

        if ($append_all) {
            $customers = array_merge([['id' => 0, 'company' => __('messages.all')]], $customers);
        }

        return $customers;
    }
}
