<?php

namespace Bican\Roles\Traits;

use Bican\Roles\Models\Permission;
use Bican\Roles\Exceptions\RoleNotFoundException;
use Bican\Roles\Exceptions\InvalidArgumentException;

trait HasRoleAndPermission
{
    /**
     * Property for caching roles.
     *
     * @var \Illuminate\Database\Eloquent\Collection|null
     */
    protected $roles;

    /**
     * Property for caching permissions.
     *
     * @var \Illuminate\Database\Eloquent\Collection|null
     */
    protected $permissions;

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
     * Get all roles as collection.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRoles()
    {
        return (!$this->roles) ? $this->roles = $this->roles()->get() : $this->roles;
    }

    /**
     * Check if the user has a role or roles.
     *
     * @param int|string|array $role
     * @param string $methodName
     * @return bool
     * @throws \Bican\Roles\Exceptions\InvalidArgumentException
     */
    public function is($role, $methodName = 'One')
    {
        if ($this->isPretendEnabled()) { return $this->pretend('is'); }

        $this->checkMethodNameArgument($methodName);

        return $this->{'is' . ucwords($methodName)}($this->getArrayFrom($role));
    }

    /**
     * Check if the user has at least one role.
     *
     * @param array $roles
     * @return bool
     */
    protected function isOne(array $roles)
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) { return true; }
        }

        return false;
    }

    /**
     * Check if the user has all roles.
     *
     * @param array $roles
     * @return bool
     */
    protected function isAll(array $roles)
    {
        foreach ($roles as $role) {
            if (!$this->hasRole($role)) { return false; }
        }

        return true;
    }

    /**
     * Check if the user has role.
     *
     * @param int|string $role
     * @return bool
     */
    protected function hasRole($role)
    {
        return $this->getRoles()->contains($role) || $this->getRoles()->contains('slug', $role);
    }

    /**
     * Attach role to a user.
     *
     * @param int|\Bican\Roles\Models\Role $role
     * @return null|bool
     */
    public function attachRole($role)
    {
        return (!$this->getRoles()->contains($role)) ? $this->roles()->attach($role) : true;
    }

    /**
     * Detach role from a user.
     *
     * @param int|\Bican\Roles\Models\Role $role
     * @return int
     */
    public function detachRole($role)
    {
        return $this->roles()->detach($role);
    }

    /**
     * Detach all roles from a user.
     *
     * @return int
     */
    public function detachAllRoles()
    {
        return $this->roles()->detach();
    }

    /**
     * Get role level of a user.
     *
     * @return int
     * @throws \Bican\Roles\Exceptions\RoleNotFoundException
     */
    public function level()
    {
        if ($role = $this->getRoles()->sortByDesc('level')->first()) { return $role->level; }

        throw new RoleNotFoundException('This user has no role.');
    }

    /**
     * Get all permissions from roles.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Bican\Roles\Exceptions\RoleNotFoundException
     */
    public function rolePermissions()
    {
        if (!$roles = $this->getRoles()->lists('id')) {
            throw new RoleNotFoundException('This user has no role.');
        }

        return Permission::select([
                    'permissions.*',
                    'permission_role.created_at as pivot_created_at',
                    'permission_role.updated_at as pivot_updated_at'
                ])->join('permission_role', 'permission_role.permission_id', '=', 'permissions.id')
                ->join('roles', 'roles.id', '=', 'permission_role.role_id')
                ->whereIn('roles.id', $roles)
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
     * Get all permissions as collection.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPermissions()
    {
        return (!$this->permissions)
                ? $this->permissions = $this->rolePermissions()->get()->merge($this->userPermissions()->get())
                : $this->permissions;
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
        if ($this->isPretendEnabled()) { return $this->pretend('can'); }

        $this->checkMethodNameArgument($methodName);

        return $this->{'can' . ucwords($methodName)}($this->getArrayFrom($permission));
    }

    /**
     * Check if the user has at least one permission.
     *
     * @param array $permissions
     * @return bool
     */
    protected function canOne(array $permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) { return true; }
        }

        return false;
    }

    /**
     * Check if the user has all permissions.
     *
     * @param array $permissions
     * @return bool
     */
    protected function canAll(array $permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) { return false; }
        }

        return true;
    }

    /**
     * Check if the user has a permission.
     *
     * @param int|string $permission
     * @return bool
     */
    protected function hasPermission($permission)
    {
        return $this->getPermissions()->contains($permission) || $this->getPermissions()->contains('slug', $permission);
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
        if ($this->isPretendEnabled()) { return $this->pretend('allowed'); }

        if ($owner === true && $entity->{$ownerColumn} == $this->id) { return true; }

        foreach ($this->getPermissions() as $permission) {
            if ($permission->model != ''
                && get_class($entity) == $permission->model
                && ($permission->id == $providedPermission || $permission->slug === $providedPermission)
            ) { return true; }
        }

        return false;
    }

    /**
     * Attach permission to a user.
     *
     * @param int|\Bican\Roles\Models\Permission $permission
     * @return null|bool
     */
    public function attachPermission($permission)
    {
        return (!$this->getPermissions()->contains($permission)) ? $this->userPermissions()->attach($permission) : true;
    }

    /**
     * Detach permission from a user.
     *
     * @param int|\Bican\Roles\Models\Permission $permission
     * @return int
     */
    public function detachPermission($permission)
    {
        return $this->userPermissions()->detach($permission);
    }

    /**
     * Detach all permissions from a user.
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
     * @return bool
     */
    private function isPretendEnabled()
    {
        return (bool) config('roles.pretend.enabled');
    }

    /**
     * Allows to pretend or simulate package behavior.
     *
     * @param string $option
     * @return bool
     */
    private function pretend($option = null)
    {
        return (bool) config('roles.pretend.options.' . $option);
    }

    /**
     * Get an array from argument.
     *
     * @param int|string|array $argument
     * @return array
     */
    private function getArrayFrom($argument)
    {
        if (!is_array($argument)) { return preg_split('/ ?[,|] ?/', $argument); }

        return $argument;
    }

    /**
     * Check methodName argument.
     *
     * @param string $methodName
     * @return void
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
            return $this->is(snake_case(substr($method, 2), config('roles.separator')));
        } elseif (starts_with($method, 'can')) {
            return $this->can(snake_case(substr($method, 3), config('roles.separator')));
        } elseif (starts_with($method, 'allowed')) {
            return $this->allowed(snake_case(substr($method, 7), config('roles.separator')), $parameters[0], (isset($parameters[1])) ? $parameters[1] : true, (isset($parameters[2])) ? $parameters[2] : 'user_id');
        }

        return parent::__call($method, $parameters);
    }
}
