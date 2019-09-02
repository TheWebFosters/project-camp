<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
    * Get all of the projects that are assigned this category.
    */
    public function projects()
    {
        return $this->morphedByMany('App\Project', 'categorable');
    }

    /**
     * retrieve category for dropdown
     *
     * @return $categories
     */
    public static function forDropdown($type, $project_id = null, $append_all = false)
    {
        $query = Category::where('type', $type)
                        ->select('id', 'name', 'project_id')
                        ->orderBy('name');

        if (!empty($project_id)) {
            $query->where('project_id', $project_id);
        }
        
        $categories = $query->get()
                        ->toArray();

        if ($append_all) {
            $categories = array_merge([['id' => 0, 'name' => __('messages.all')]], $categories);
        }
        
        return $categories;
    }
}
