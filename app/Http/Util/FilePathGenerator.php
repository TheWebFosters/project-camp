<?php

namespace App\Http\Util;

use Spatie\MediaLibrary\Models\Media;
use Spatie\MediaLibrary\PathGenerator\PathGenerator;

class FilePathGenerator implements PathGenerator
{
    public function getPath(Media $media) : string
    {
        return ($media->collection_name.'/'.$media->id).'/';
    }

    public function getPathForConversions(Media $media) : string
    {
        return $this->getPath($media).'c/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getPath($media).'/cri/';
    }
}
