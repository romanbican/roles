<?php namespace Bican\Roles;

use Illuminate\Database\Eloquent\Collection;
use Bican\Roles\Exceptions\RoleNotFoundException;

trait HasPermission {

    /**
     * Get all role permissions.
     *
     * @return Collection
     * @throws RoleNotFoundException
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
                ->groupBy('permissions.id')->get(['permissions.*']);
    }

    /**
     * User belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function userPermissions()
    {
        return $this->belongsToMany('Bican\Roles\Permission');
    }

    /**
     * Merge role permissions and user permissions.
     *
     * @return Collection
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
     * @throws InvalidArgumentException
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
     * @param Collection $userPermissions
     * @return bool
     */
    private function canOne(array $permissions, Collection $userPermissions)
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
     * @param Collection $userPermissions
     * @return bool
     */
    private function canAll(array $permissions, Collection $userPermissions)
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
     * @param Collection $userPermissions
     * @return bool
     */
    private function hasPermission($providedPermission, Collection $userPermissions)
    {
        foreach ($userPermissions as $permission)
        {
            if ($permission->id == $providedPermission || $permission->label === $providedPermission)
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
            if ($permission->model != '' && get_class($entity) == $permission->model && ($permission->id == $providedPermission || $permission->label === $providedPermission))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Attach permission.
     *
     * @param int|array|Permission $permission
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
     * @param int|Permission $permission
     * @return mixed
     */
    public function detachPermission($permission)
    {
        return $this->userPermissions()->detach($permission);
    }

}