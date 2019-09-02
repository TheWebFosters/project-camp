<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class KnowledgeBase extends Model implements HasMedia
{
    use HasMediaTrait;
     
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Get all of the category for the knowladge base.
     */
    public function categories()
    {
        return $this->morphToMany('App\Category', 'categorable');
    }

    /**
     * User who created the knowledge base
     */
    public function user()
    {
        return $this->belongsTo('App\Components\User\Models\User', 'created_by');
    }
}
