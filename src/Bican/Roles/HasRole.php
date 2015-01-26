<?php namespace Bican\Roles;

trait HasRole {

    /**
     * User belongs to many roles.
     *
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany('Bican\Roles\Role');
    }

    /**
     * Check if a user has any of the provided roles
     *
     * @param int|string|array $providedRoles
     * @return bool
     */
    public function hasRole($providedRoles)
    {
        if(!is_array($providedRoles))
        {
            $providedRoles = array($providedRoles);
        }
        
        foreach($providedRoles as $role)
        {
            if($this->hasSingleRole($role))
            {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if a user has provided role.
     *
     * @param int|string $providedRole
     * @return bool
     */
    public function hasSingleRole($providedRole)
    {
        foreach ($this->roles()->get() as $role)
        {
            if ($role->id === $providedRole || $role->name === $providedRole) return true;
        }

        return false;
    }

    /**
     * Attach role.
     *
     * @param int|Role $role
     * @return mixed
     */
    public function attachRole($role)
    {
        return $this->roles()->attach($role);
    }

    /**
     * Detach role.
     *
     * @param int|Role $role
     * @return mixed
     */
    public function detachRole($role)
    {
        return $this->roles()->detach($role);
    }

}