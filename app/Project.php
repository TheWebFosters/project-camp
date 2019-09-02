<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Project extends Model
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
    * The accessors to append to the model's array form.
    *
    * @var array
    */

    /**
     * The member that belong to the project.
     */
    public function members()
    {
        return $this->belongsToMany('App\Components\User\Models\User', 'project_members', 'project_id', 'user_id');
    }

    /**
     * Return the project lead
     */
    public function lead()
    {
        return $this->belongsTo('App\Components\User\Models\User', 'lead_id');
    }

    /**
     * Get the customer for the project.
     */
    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

    /**
     * Get the project task for the project.
     */
    public function tasks()
    {
        return $this->hasMany('App\ProjectTask');
    }
    
    /**
     * Get the milestone for the project.
     */
    public function milestones()
    {
        return $this->hasMany('App\ProjectMilestone');
    }

    /**
     * Get the comments for the project.
     */
    public function comments()
    {
        return $this->hasMany('App\ProjectComment');
    }

    /**
     * Get the transactions for the project.
     */
    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }

    /**
     * Get all of the category for the project.
     */
    public function categories()
    {
        return $this->morphToMany('App\Category', 'categorable');
    }

    /**
     * Return the project creator
     */
    public function creator()
    {
        return $this->belongsTo('App\Components\User\Models\User', 'created_by');
    }

    /**
     * Get all of the projects's notes.
     */
    public function notes()
    {
        return $this->morphMany('App\Note', 'notable');
    }

    /**
     * Return the billing_types for the project.
     */
    public static function getBillingTypes()
    {
        $billing_types = [
                            ['key' => 'fixed_rate',
                             'value' => __('messages.fixed_rate'),
                            ],
                            ['key' => 'project_hours',
                             'value' => __('messages.project_hours'),
                            ],
                            ['key' => 'task_hours',
                             'value' => __('messages.task_hours'),
                            ],
                        ];

        return $billing_types;
    }

    /**
     * Return the status for the project.
     */
    public static function getStatusForProject()
    {
        $status = [
                    ['key' => 'not_started',
                     'value' => __('messages.not_started'),
                    ],
                    ['key' => 'in_progress',
                     'value' => __('messages.in_progress'),
                    ],
                    ['key' => 'on_hold',
                     'value' => __('messages.on_hold'),
                    ],
                    ['key' => 'cancelled',
                     'value' => __('messages.cancelled'),
                    ],
                    ['key' => 'completed',
                     'value' => __('messages.completed'),
                    ],
                ];

        return $status;
    }

    /**
     * A project can be favorited.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function favorites()
    {
        return $this->morphMany(Favorite::class, 'favoritable');
    }

    /**
     * Favorite the current project.
     *
     * @return Model
     */
    public function favorite()
    {
        $attributes = ['user_id' => auth()->id()];
        if (! $this->favorites()->where($attributes)->exists()) {
            return $this->favorites()->create($attributes);
        }
    }

    /**
     * Unfavorite the current project.
     */
    public function unfavorite()
    {
        $this->favorites()->where('user_id', auth()->id())->get()->each->delete();
    }

    /**
     * Determine if the current project has been favorited.
     *
     * @return boolean
     */
    public function isFavorited()
    {
        return $this->favorites()->where('user_id', auth()->id())->exists();
    }

    /**
     * Fetch the favorited status as a property.
     *
     * @return bool
     */
    public function getIsFavoritedAttribute()
    {
        return $this->isFavorited();
    }

    /**
     * retrieve projects for dropdown
     *
     * @return $customers
     */
    public static function getProjectsList($append_all = false)
    {
        $projects = Project::select('id', 'name')
                        ->get()
                        ->toArray();

        if ($append_all) {
            $projects = array_merge([['id' => 0, 'name' => __('messages.all')]], $projects);
        }

        return $projects;
    }
}
