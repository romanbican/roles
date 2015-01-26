<?php namespace Bican\Roles;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['label', 'name', 'level'];

    /**
     * Role belongs to many permissions.
     *
     * @return mixed
     */
    public function permissions()
    {
        return $this->belongsToMany('Bican\Roles\Permission');
    }

    /**
     * Attach permission or permissions.
     *
     * @param int|array|Permission $permission
     * @return mixed
     */
    public function attachPermission($permission)
    {
        if( ! is_array($permission))
        {
            $permission = [$permission];
        }

        foreach ($permission as $perm)
        {
            $this->permissions()->attach($perm);
        }

        return true;
    }

    /**
     * Detach permission or permissions.
     *
     * @param int|array|Permission $permission
     * @return mixed
     */
    public function detachPermission($permission)
    {
        if( ! is_array($permission))
        {
            $permission = [$permission];
        }

        foreach ($permission as $perm)
        {
            $this->permissions()->detach($perm);
        }

        return true;
    }

}