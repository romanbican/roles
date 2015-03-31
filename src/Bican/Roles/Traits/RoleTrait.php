<?php

namespace Bican\Roles\Traits;

use Illuminate\Support\Facades\Config;

trait RoleTrait
{
    /**
     * Role belongs to many permissions.
     *
     * @return mixed
     */
    public function permissions()
    {
        return $this->belongsToMany('Bican\Roles\Models\Permission')->withTimestamps();
    }

    /**
     * Role belongs to many users.
     *
     * @return mixed
     */
    public function users()
    {
        return $this->belongsToMany(Config::get('auth.model'))->withTimestamps();
    }

    /**
     * Attach permission.
     *
     * @param int|\Bican\Roles\Models\Permission $permission
     * @return mixed
     */
    public function attachPermission($permission)
    {
        if (!$this->permissions()->get()->contains($permission)) {
            return $this->permissions()->attach($permission);
        }

        return true;
    }

    /**
     * Detach permission.
     *
     * @param int|\Bican\Roles\Models\Permission $permission
     * @return mixed
     */
    public function detachPermission($permission)
    {
        return $this->permissions()->detach($permission);
    }

    /**
     * Detach all permissions.
     *
     * @return int
     */
    public function detachAllPermissions()
    {
        return $this->permissions()->detach();
    }
}
