<?php

namespace Bican\Roles\Traits;

use Bican\Roles\Models\Permission;
use Illuminate\Support\Facades\Config;
use Illuminate\Database\Eloquent\Collection;
use Bican\Roles\Exceptions\RoleNotFoundException;
use Bican\Roles\Exceptions\InvalidArgumentException;

trait HasRoleAndPermission
{
    /**
     * User belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('Bican\Roles\Models\Role')->withTimestamps();
    }

    /**
     * Check if the user has a provided role or roles.
     *
     * @param int|string|array $role
     * @param string $methodName
     * @return bool
     * @throws \Bican\Roles\Exceptions\InvalidArgumentException
     */
    public function is($role, $methodName = 'One')
    {
        if ($this->isPretendEnabled()) {
            return $this->pretend('is');
        }

        $this->checkMethodNameArgument($methodName);

        if ($this->{'is' . ucwords($methodName)}($this->getArrayFrom($role), $this->roles()->get())) {
            return true;
        }

        return false;
    }

    /**
     * Check if the user has at least one of provided roles.
     *
     * @param array $roles
     * @param \Illuminate\Database\Eloquent\Collection $userRoles
     * @return bool
     */
    protected function isOne(array $roles, Collection $userRoles)
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role, $userRoles)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the user has all provided roles.
     *
     * @param array $roles
     * @param \Illuminate\Database\Eloquent\Collection $userRoles
     * @return bool
     */
    protected function isAll(array $roles, Collection $userRoles)
    {
        foreach ($roles as $role) {
            if (!$this->hasRole($role, $userRoles)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the user has provided role.
     *
     * @param int|string $providedRole
     * @param \Illuminate\Database\Eloquent\Collection $userRoles
     * @return bool
     */
    protected function hasRole($providedRole, Collection $userRoles)
    {
        foreach ($userRoles as $role) {
            if ($role->id == $providedRole || str_is($providedRole, $role->slug)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Attach role.
     *
     * @param int|\Bican\Roles\Models\Role $role
     * @return mixed
     */
    public function attachRole($role)
    {
        if (!$this->roles()->get()->contains($role)) {
            return $this->roles()->attach($role);
        }

        return true;
    }

    /**
     * Detach role.
     *
     * @param int|\Bican\Roles\Models\Role $role
     * @return mixed
     */
    public function detachRole($role)
    {
        return $this->roles()->detach($role);
    }

    /**
     * Detach all roles.
     *
     * @return mixed
     */
    public function detachAllRoles()
    {
        return $this->roles()->detach();
    }

    /**
     * Get users level.
     *
     * @return int
     * @throws \Bican\Roles\Exceptions\RoleNotFoundException
     */
    public function level()
    {
        if ($role = $this->roles()->orderBy('level', 'desc')->first()) {
            return $role->level;
        }

        throw new RoleNotFoundException('This user has no role.');
    }

    /**
     * Scope selecting users by provided role.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $role
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRole($query, $role)
    {
        return $query->whereHas('roles', function ($query) use ($role) {
            $query->where('slug', $role);
        });
    }

    /**
     * Get all role permissions.
     *
     * @return
     * @throws \Bican\Roles\Exceptions\RoleNotFoundException
     */
    public function rolePermissions()
    {
        if (!$rolesList = $this->roles()->select('roles.id')->lists('roles.id')) {
            throw new RoleNotFoundException('This user has no role.');
        }

        return Permission::select([
                    'permissions.*',
                    'permission_role.created_at as pivot_created_at',
                    'permission_role.updated_at as pivot_updated_at'
                ])->join('permission_role', 'permission_role.permission_id', '=', 'permissions.id')
                ->join('roles', 'roles.id', '=', 'permission_role.role_id')
                ->whereIn('roles.id', $rolesList)
                ->orWhere('roles.level', '<', $this->level())
                ->groupBy('permissions.id');
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
        return $this->rolePermissions()->get()->merge($this->userPermissions()->get());
    }

    /**
     * Check if the user has a permission or permissions.
     *
     * @param int|string|array $permission
     * @param string $methodName
     * @param string $from
     * @return bool
     * @throws \Bican\Roles\Exceptions\InvalidArgumentException
     */
    public function can($permission, $methodName = 'One', $from = '')
    {
        if ($this->isPretendEnabled()) {
            return $this->pretend('can');
        }

        $this->checkMethodNameArgument($methodName);

        $allPermissions = ($from != 'role' && $from != 'user') ? $this->permissions() : $this->{$from . 'Permissions'}()->get();

        if ($this->{'can' . ucwords($methodName)}($this->getArrayFrom($permission), $allPermissions)) {
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
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission, $userPermissions)) {
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
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission, $userPermissions)) {
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
        foreach ($userPermissions as $permission) {
            if ($permission->id == $providedPermission || str_is($providedPermission, $permission->slug)) {
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
     * @param string $ownerColumn
     * @return bool
     */
    public function allowed($providedPermission, $entity, $owner = true, $ownerColumn = 'user_id')
    {
        if ($this->isPretendEnabled()) {
            return $this->pretend('allowed');
        }

        if ($owner === true && $entity->{$ownerColumn} == $this->id) {
            return true;
        }

        foreach ($this->permissions() as $permission) {
            if ($permission->model != '' && get_class($entity) == $permission->model && ($permission->id == $providedPermission || $permission->slug === $providedPermission)) {
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
        if (!$this->userPermissions()->get()->contains($permission)) {
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
     * Check if pretend option is enabled.
     *
     * @return boolean
     */
    private function isPretendEnabled()
    {
        return (bool) Config::get('roles.pretend.enabled');
    }

    /**
     * Allows to pretend or simulate package behavior.
     *
     * @param string $option
     * @return boolean
     */
    private function pretend($option = null)
    {
        return (bool) Config::get('roles.pretend.options.' . $option);
    }

    /**
     * Get an array from provided parameter.
     *
     * @param int|string|array $value
     * @return array
     */
    private function getArrayFrom($value)
    {
        if (!is_array($value)) {
            return preg_split('/ ?[,|] ?/', $value);
        }

        return $value;
    }

    /**
     * Check methodName argument.
     *
     * @param string $methodName
     * @return mixed
     * @throws \Bican\Roles\Exceptions\InvalidArgumentException
     */
    private function checkMethodNameArgument($methodName)
    {
        if (ucwords($methodName) != 'One' && ucwords($methodName) != 'All') {
            throw new InvalidArgumentException('You can pass only strings [one] or [all] as a second parameter in [is] or [can] method.');
        }
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (starts_with($method, 'is')) {
            if ($this->is(snake_case(substr($method, 2), Config::get('roles.separator')))) {
                return true;
            }

            return false;
        } elseif (starts_with($method, 'can')) {
            if ($this->can(snake_case(substr($method, 3), Config::get('roles.separator')))) {
                return true;
            }

            return false;
        } elseif (starts_with($method, 'allowed')) {
            if ($this->allowed(snake_case(substr($method, 7), Config::get('roles.separator')), $parameters[0], (isset($parameters[1])) ? $parameters[1] : true, (isset($parameters[2])) ? $parameters[2] : 'user_id')) {
                return true;
            }

            return false;
        }

        return parent::__call($method, $parameters);
    }
}
