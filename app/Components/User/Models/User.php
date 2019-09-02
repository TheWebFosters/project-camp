<?php

namespace App\Components\User\Models;

use App\System;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User.
 *
 * @property int         $id
 * @property string      $name
 * @property string      $email
 * @property array       $permissions
 * @property string|null $active
 */
class User extends Authenticatable implements HasMedia
{
    use Notifiable,
        LogsActivity,
        HasRoles,
        HasMediaTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    protected static $logUnguarded = true;
    protected static $logOnlyDirty = true;
    protected $appends = ['avatar_url'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * the validation rules.
     *
     * @var array
     */
    public static $rules = [];

    /**
     * hashes password.
     *
     * @param $password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    /**
     * register medias for users
     *
     */
    public function registerMediaCollections()
    {
        $this
            ->addMediaCollection('avatar')
            ->singleFile();
    }

    /**
     * logs last login date of the user.
     */
    public function logLastLogin()
    {
        $this->last_login = $this->freshTimestamp();
        $this->save();
    }

    /**
     * Scope a query to only include Employess.
     * (one that does not have customer_id).
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEmployees($query)
    {
        return $query->where('customer_id', null);
    }

    public function scopeOfName($query, $name)
    {
        if (null === $name || '' === $name) {
            return false;
        }

        return $query->where('name', 'LIKE', "%{$name}%");
    }

    public function scopeOfEmail($query, $email)
    {
        if (null === $email || '' === $email) {
            return false;
        }

        return $query->where('email', '=', $email);
    }

    /**
     * Get all of the employee's notes.
     */
    public function notes()
    {
        return $this->morphMany('App\Note', 'notable');
    }
    
    /**
     * Get the leaves for the employee.
     */
    public function leaves()
    {
        return $this->hasMany('App\Leave');
    }

    /**
     * Get user avatar.
     */
    public function getAvatarUrlAttribute()
    {
        return optional($this->getMedia('avatar')->first())
            ->getFullUrl();
    }

    /**
     * Get the user type (employee or client).
     *
     * @return string
     */
    public function getUserTypeAttribute()
    {
        if (empty($this->customer_id)) {
            return 'employee';
        } else {
            return 'client';
        }
    }

    /**
     * Check if user type is employee.
     *
     * @return bool
     */
    public function getIsEmployeeAttribute()
    {
        return empty($this->customer_id);
    }

    /**
     * Check if user type is client.
     *
     * @return bool
     */
    public function getIsClientAttribute()
    {
        return !empty($this->customer_id);
    }

    /**
     * Customer for the contact.
     */
    public function customer()
    {
        return $this->belongsTo('App\Customer');
    }

    /**
     * retrieve users for dropdown.
     *
     * @return $users
     */
    public static function getUsersForDropDown($append_all = false)
    {
        $users = User::where('customer_id', null)
                        ->select('id', 'name')
                        ->orderBy('name')
                        ->get()
                        ->toArray();

        if ($append_all) {
            $users = array_merge([['id' => 0, 'name' => __('messages.all')]], $users);
        }
        
        return $users;
    }

    public static function getRolesForEmployee()
    {
        $roles = Role::where('type', 'employee')
                    ->get()
                    ->toArray();
                    
        return $roles;
    }

    /**
     * retrieve gender for dropdown.
     *
     * @return array
     */
    public static function getGenders()
    {
        return [
                ['value' => 'male',
                 'text' => __('messages.male'),
                ],
                ['value' => 'female',
                 'text' => __('messages.female'),
                ],
                ['value' => 'other',
                 'text' => __('messages.other'),
                ],
            ];
    }

    /**
     * retrieve all the permissions
     * of user.
     *
     * @return array
     */
    public static function getUserPermissions($user)
    {
        $userPermissions = $user->getAllPermissions();

        $permissions = [];
        foreach ($userPermissions as $userPermission) {
            $permissions[$userPermission->name] = 1;
        }

        if (($user->hasRole('superadmin'))) {
            $permissions['superadmin'] = 1;
        }

        return $permissions;
    }

    /**
     * retrieve all the roles
     * of user.
     *
     * @return array
     */
    public static function getUserRoles($user)
    {
        $user_roles = $user->roles;

        $roles = [];
        foreach ($user_roles as $user_role) {
            $roles[$user_role->name] = 1;
        }

        if (($user->hasRole('superadmin'))) {
            $permissions['superadmin'] = 1;
        }

        return $roles;
    }

    /**
     * retrieve date format
     * of app.
     *
     */
    public static function appDateFormat()
    {
        $value = System::where('key', 'date_format')->value('value');

        if ($value == 'd-m-Y') {
            return ['KEY' => 'd-m-Y', 'VALUE' => 'DD-MM-YYYY'];
        } elseif ($value == 'm-d-Y') {
            return ['KEY' => 'm-d-Y', 'VALUE' => 'MM-DD-YYYY'];
        } elseif ($value == 'd/m/Y') {
            return ['KEY' => 'd/m/Y', 'VALUE' => 'DD/MM/YYYY'];
        } elseif ($value == 'm/d/Y') {
            return ['KEY' => 'm/d/Y', 'VALUE' => 'MM/DD/YYYY'];
        }
    }

    /**
     * retrieve time format
     * of app.
     *
     */
    public static function appTimeFormat()
    {
        $value = System::where('key', 'time_format')->value('value');

        return $value;
    }
}
