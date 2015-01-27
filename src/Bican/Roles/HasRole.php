<?php namespace Bican\Roles;

use Bican\Roles\Exceptions\RoleNotFoundException;
use Bican\Roles\Exceptions\BadMethodCallException;

trait HasRole {

    /**
     * User has one role.
     *
     * @return mixed
     * @throws RoleNotFoundException
     */
    public function role()
    {
        if ( ! $role = $this->belongsTo('Bican\Roles\Role')->first())
        {
            throw new RoleNotFoundException('Role [' . $this->role_id . '] doest not exist.');
        }

        return $role;
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
                        ->where('roles.id', '=', $this->role_id)->orWhere('roles.level', '<=', $this->role()->level)->where('permissions.unique', '=', 0)
                        ->groupBy('permissions.id')->get(['permissions.*']);
    }

    /**
     * Check if a user has role.
     *
     * @param int|string $role
     * @return bool
     */
    public function is($role)
    {
        if ($this->role_id === $role || $this->role()->label === $role)
        {
            return true;
        }

        return false;
    }

    /**
     * Check if a user has provided permission or permissions.
     *
     * @param int|string|array $permissions
     * @param string $methodName
     * @return bool
     */
    public function can($permissions, $methodName = 'One')
    {
        $permissions = $this->getPermissionsArray($permissions);

        if ($this->{'can' . ucwords($methodName)}($permissions))
        {
            return true;
        }

        return false;
    }

    /**
     * Check if user has at least one of provided permissions.
     *
     * @param array $permissions
     * @return bool
     */
    public function canOne(array $permissions)
    {
        foreach ($permissions as $permission)
        {
            if ($this->hasPermission($permission))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user has all provided permissions.
     *
     * @param array $permissions
     * @return bool
     */
    public function canAll(array $permissions)
    {
        foreach ($permissions as $permission)
        {
            if ( ! $this->hasPermission($permission))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a user has provided permission.
     *
     * @param int|string $providedPermission
     * @return bool
     */
    public function hasPermission($providedPermission)
    {
        foreach ($this->permissions() as $permission)
        {
            if ($permission->id == $providedPermission || $permission->label === $providedPermission)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Get level.
     *
     * @return int
     */
    public function level()
    {
        return $this->role()->level;
    }

    /**
     * Assign (change) role.
     *
     * @param int|Role $role
     * @return mixed
     */
    public function assignRole($role)
    {
        if (is_object($role))
        {
            $role = $role->id;
        }

        $this->role_id = $role;

        return $this->save();
    }

    /**
     * Get permissions as an array.
     *
     * @param int|string|array $permissions
     * @return array
     */
    private function getPermissionsArray($permissions)
    {
        if ( ! is_array($permissions))
        {
            $permissions = preg_split('/ ?[,|] ?/', $permissions);
        }

        return $permissions;
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (starts_with($method, 'is'))
        {
            if($this->is(snake_case(substr($method, 2))))
            {
                return true;
            }

            return false;
        }
        elseif (starts_with($method, 'can'))
        {
            if($this->can(snake_case(substr($method, 3))))
            {
                return true;
            }

            return false;
        }

        throw new BadMethodCallException('Method [' . $method . '] does not exist.');
    }

}