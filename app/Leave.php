<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Leave extends Model
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
     * Return the status for the leave.
     */
    public static function getStatusForLeave($append_all = false)
    {
        $status = [
                    ['key' => 'approved',
                     'value' => __('messages.approved'),
                    ],
                    ['key' => 'cancelled',
                     'value' => __('messages.cancelled'),
                    ],
                    ['key' => 'pending',
                     'value' => __('messages.pending'),
                    ],
                ];

        if ($append_all) {
            $status = array_merge([['key' => '', 'value' => __('messages.all')]], $status);
        }
        
        return $status;
    }

    /**
     * Return the date filter for the leave.
     */
    public static function getDateFilter()
    {
        $date = [
                    ['key' => '',
                     'value' => __('messages.all'),
                    ],
                    ['key' => 'now',
                     'value' => __('messages.now'),
                    ],
                    ['key' => 'upcoming',
                     'value' => __('messages.upcoming'),
                    ],
                    ['key' => 'past',
                     'value' => __('messages.past'),
                    ],
                ];
        
        return $date;
    }
}
