<?php namespace Bican\Roles;

trait HasRole {

    /**
     * User has one role.
     *
     * @return mixed
     */
    public function role()
    {
        return $this->belongsTo('Bican\Roles\Role')->first();
    }

    /**
     * User has many permissions.
     *
     * @return mixed
     */
    public function permissions()
    {
        return Permission::join('permission_role', 'permission_role.permission_id', '=', 'permissions.id')
                    ->join('roles', 'roles.id', '=', 'permission_role.role_id')
                    ->where('roles.id', '=', $this->role_id)
                    ->orWhere('roles.level', '<=', $this->role()->level)
                    ->where('permissions.unique', '=', 0)
                    ->groupBy('permissions.id')
                    ->get(['permissions.id', 'permissions.label', 'permissions.name']);
    }

    /**
     * Check if a user has role.
     *
     * @param int|string $providedRole
     * @return bool
     */
    public function hasRole($providedRole)
    {
        if ($this->role_id === $providedRole || $this->role()->label === $providedRole)
        {
            return true;
        }

        return false;
    }

    /**
     * Change role.
     *
     * @param int|Role $role
     * @return mixed
     */
    public function changeRole($role)
    {
        if (is_object($role))
        {
            $role = $role->getKey();
        }

        $this->role_id = $role;

        return $this->save();
    }

    /**
     * Check if a user has any of the provided permissions.
     *
     * @param int|string|array $providedPermissions
     * @return bool
     */
    public function hasPermission($providedPermissions)
    {
        if ( ! is_array($providedPermissions))
        {
            $providedPermissions = [$providedPermissions];
        }

        foreach ($providedPermissions as $permission)
        {
            if ($this->hasSinglePermission($permission))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if a user has provided permission.
     *
     * @param int|string $providedPermission
     * @return bool
     */
    public function hasSinglePermission($providedPermission)
    {
        foreach ($this->permissions() as $permission)
        {
            if ($permission->id === $providedPermission || $permission->label === $providedPermission)
            {
                return true;
            }
        }

        return false;
    }

}