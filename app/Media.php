<?php

namespace App;

use Spatie\MediaLibrary\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'disk', 'collection_name', 'manipulations', 'name', 'order_column', 'responsive_images', 'size', 'custom_properties'
    ];

    protected $appends = ['full_url', 'download_url', 'display_name'];

    /**
     * Get full url.
     */
    public function getFullUrlAttribute()
    {
        return $this->getFullUrl();
    }

    /**
     * Get download link.
     */
    public function getDownloadUrlAttribute()
    {
        return action('MediaController@show', $this->id);
    }

    /**
     * Get display name for the media.
     */
    public function getDisplayNameAttribute()
    {
        $data = explode('_', $this->file_name);

        return $data[1];
    }
}
