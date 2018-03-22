<?php

namespace Bican\Roles\Traits;

trait PermissionHasRelations
{
    /**
     * Permission belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(config('roles.models.role'))->withTimestamps();
    }

    /**
     * Permission belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('auth.model'))->withTimestamps();
    }

    /**
     * Attach role to a permission.
     */
    public function attachRole($role)
    {
        return (!$this->roles()->get()->contains($role)) ? $this->roles()->attach($role) : true;
    }

    /**
     * Detach role from a permission.
     */
    public function detachRole($role)
    {
        return $this->roles()->detach($role);
    }

    /**
     * Detach all roles.
     *
     * @return int
     */
    public function detachAllRoles()
    {
        return $this->roles()->detach();
    }
}
