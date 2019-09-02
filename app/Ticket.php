<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Return the status list for the ticket.
     */
    public static function StatusForDropDown($append_all = false)
    {
        $statuses = [
                        [
                            'key' => 'new',
                            'value' => __('messages.new')
                        ],
                        [
                            'key' => 'open',
                            'value' => __('messages.open')
                        ],
                        [
                            'key' => 'closed',
                            'value' => __('messages.closed')
                        ]
                    ];

        if ($append_all) {
            $statuses = array_merge([['key' => '', 'value' => __('messages.all')]], $statuses);
        }
        
        return $statuses;
    }

    public function ticketType()
    {
        return $this->hasOne('App\Category', 'id', 'category_id');
    }

    /**
     * User who last updated
     */
    public function lastUpdateBy()
    {
        return $this->belongsTo('App\Components\User\Models\User', 'updated_by');
    }
}
