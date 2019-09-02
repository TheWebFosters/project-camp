<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function getStatus($append_all = false)
    {
        $statuses = Status::select('id', 'name')
                        ->get()
                        ->toArray();

        if ($append_all) {
            $statuses = array_merge([['id' => 0, 'name' => __('messages.all')]], $statuses);
        }
        
        return $statuses;
    }
}
