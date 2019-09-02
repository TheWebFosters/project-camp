<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProjectTask extends Model
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
     * The member that belong to the task.
     */
    public function taskMembers()
    {
        return $this->belongsToMany('App\Components\User\Models\User', 'project_task_members', 'project_task_id', 'user_id');
    }
    
    /**
     * Return the creator of task.
     */
    public function taskCreator()
    {
        return $this->belongsTo('App\Components\User\Models\User', 'created_by');
    }

    /**
     * Return the prokect that belongs to task.
     */
    public function project()
    {
        return $this->belongsTo('App\Project', 'project_id');
    }
    
    /**
     * Get all of the task's notes.
     */
    public function notes()
    {
        return $this->morphMany('App\Note', 'notable');
    }

    /**
     * Get all of the category for the task.
     */
    public function category()
    {
        return $this->belongsTo('App\Category');
    }
}
