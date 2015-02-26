<?php namespace Bican\Roles\Traits;

use Bican\Roles\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Bican\Roles\Exceptions\RoleNotFoundException;

trait HasPermission {

    /**
     * Get all role permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws \Bican\Roles\Exceptions\RoleNotFoundException
     */
    public function rolePermissions()
    {
        if ( ! $rolesList = $this->roles()->select('roles.id')->lists('roles.id'))
        {
            throw new RoleNotFoundException('This user has no role.');
        }

        return Permission::join('permission_role', 'permission_role.permission_id', '=', 'permissions.id')
                ->join('roles', 'roles.id', '=', 'permission_role.role_id')
                ->whereIn('roles.id', $rolesList)->orWhere('roles.level', '<', $this->level())->where('permissions.unique', '=', 0)
                ->groupBy('permissions.id')->get(['permissions.*', 'permission_role.created_at as pivot_created_at', 'permission_role.updated_at as pivot_updated_at']);
    }

    /**
     * User belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function userPermissions()
    {
        return $this->belongsToMany('Bican\Roles\Models\Permission')->withTimestamps();
    }

    /**
     * Merge role permissions and user permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function permissions()
    {
        return $this->rolePermissions()->merge($this->userPermissions()->get());
    }

    /**
     * Check if the user has a permission or permissions.
     *
     * @param int|string|array $permission
     * @param string $methodName
     * @return bool
     * @throws \Bican\Roles\Exceptions\InvalidArgumentException
     */
    public function can($permission, $methodName = 'One')
    {
        $this->checkMethodNameArgument($methodName);

        $permissions = $this->getArrayFrom($permission);

        if ($this->{'can' . ucwords($methodName)}($permissions, $this->permissions()))
        {
            return true;
        }

        return false;
    }

    /**
     * Check if the user has at least one of provided permissions.
     *
     * @param array $permissions
     * @param \Illuminate\Database\Eloquent\Collection $userPermissions
     * @return bool
     */
    protected function canOne(array $permissions, Collection $userPermissions)
    {
        foreach ($permissions as $permission)
        {
            if ($this->hasPermission($permission, $userPermissions))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the user has all provided permissions.
     *
     * @param array $permissions
     * @param \Illuminate\Database\Eloquent\Collection $userPermissions
     * @return bool
     */
    protected function canAll(array $permissions, Collection $userPermissions)
    {
        foreach ($permissions as $permission)
        {
            if ( ! $this->hasPermission($permission, $userPermissions))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the user has a provided permission.
     *
     * @param int|string $providedPermission
     * @param \Illuminate\Database\Eloquent\Collection $userPermissions
     * @return bool
     */
    protected function hasPermission($providedPermission, Collection $userPermissions)
    {
        foreach ($userPermissions as $permission)
        {
            if ($permission->id == $providedPermission || $permission->slug === $providedPermission)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the user is allowed to manipulate with entity.
     *
     * @param string $providedPermission
     * @param object $entity
     * @param bool $owner
     * @return bool
     */
    public function allowed($providedPermission, $entity, $owner = true)
    {
        if ($owner === true && $entity->user_id == $this->id)
        {
            return true;
        }

        foreach ($this->permissions() as $permission)
        {
            if ($permission->model != '' && get_class($entity) == $permission->model && ($permission->id == $providedPermission || $permission->slug === $providedPermission))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Attach permission.
     *
     * @param int|\Bican\Roles\Models\Permission $permission
     * @return mixed
     */
    public function attachPermission($permission)
    {
        if ( ! $this->userPermissions()->get()->contains($permission))
        {
            return $this->userPermissions()->attach($permission);
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
        return $this->userPermissions()->detach($permission);
    }

    /**
     * Detach all permissions.
     *
     * @return int
     */
    public function detachAllPermissions()
    {
        return $this->userPermissions()->detach();
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (starts_with($method, 'is'))
        {
            if ($this->is(snake_case(substr($method, 2), '.')))
            {
                return true;
            }

            return false;
        }
        elseif (starts_with($method, 'can'))
        {
            if ($this->can(snake_case(substr($method, 3), '.')))
            {
                return true;
            }

            return false;
        }
        elseif (starts_with($method, 'allowed'))
        {
            if ($this->allowed(snake_case(substr($method, 7), '.'), $parameters[0], (isset($parameters[1])) ? $parameters[1] : true))
            {
                return true;
            }

            return false;
        }

        return parent::__call($method, $parameters);
    }

}