<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function getSource($append_all = false)
    {
        $sources = Source::select('id', 'name')
                        ->get()
                        ->toArray();

        if ($append_all) {
            $sources = array_merge([['id' => 0, 'name' => __('messages.all')]], $sources);
        }
        
        return $sources;
    }
}
