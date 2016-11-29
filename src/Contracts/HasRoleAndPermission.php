<?php

namespace Ultraware\Roles\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Ultraware\Roles\Models\Permission;
use Ultraware\Roles\Models\Role;

interface HasRoleAndPermission
{
    /**
     * User belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles();

    /**
     * Get all roles as collection.
     *
     * @return Collection
     */
    public function getRoles();

    /**
     * Check if the user has a role or roles.
     *
     * @param int|string|array $role
     * @param bool $all
     * @return bool
     */
    public function hasRole($role, $all = false);

    /**
     * Check if the user has at least one of the given roles.
     *
     * @param int|string|array $role
     * @return bool
     */
    public function hasOneRole($role);

    /**
     * Check if the user has all roles.
     *
     * @param int|string|array $role
     * @return bool
     */
    public function hasAllRoles($role);

    /**
     * Check if the user has role.
     *
     * @param int|string $role
     * @return bool
     */
    public function checkRole($role);

    /**
     * Attach role to a user.
     *
     * @param int|Role $role
     * @return null|bool
     */
    public function attachRole($role);

    /**
     * Detach role from a user.
     *
     * @param int|Role $role
     * @return int
     */
    public function detachRole($role);

    /**
     * Detach all roles from a user.
     *
     * @return int
     */
    public function detachAllRoles();

    /**
     * Sync roles for a user.
     *
     * @param array|Role[]|Collection $roles
     * @return array
     */
    public function syncRoles($roles);

    /**
     * Get role level of a user.
     *
     * @return int
     */
    public function level();

    /**
     * Get all permissions from roles.
     *
     * @return Builder
     */
    public function rolePermissions();

    /**
     * User belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function userPermissions();

    /**
     * Get all permissions as collection.
     *
     * @return Collection
     */
    public function getPermissions();

    /**
     * Check if the user has a permission or permissions.
     *
     * @param int|string|array $permission
     * @param bool $all
     * @return bool
     */
    public function hasPermission($permission, $all = false);

    /**
     * Check if the user has at least one of the given permissions.
     *
     * @param int|string|array $permission
     * @return bool
     */
    public function hasOnePermission($permission);

    /**
     * Check if the user has all permissions.
     *
     * @param int|string|array $permission
     * @return bool
     */
    public function hasAllPermissions($permission);

    /**
     * Check if the user has a permission.
     *
     * @param int|string $permission
     * @return bool
     */
    public function checkPermission($permission);

    /**
     * Check if the user is allowed to manipulate with entity.
     *
     * @param string $providedPermission
     * @param Model $entity
     * @param bool $owner
     * @param string $ownerColumn
     * @return bool
     */
    public function allowed($providedPermission, Model $entity, $owner = true, $ownerColumn = 'user_id');

    /**
     * Attach permission to a user.
     *
     * @param int|Permission $permission
     * @return null|bool
     */
    public function attachPermission($permission);

    /**
     * Detach permission from a user.
     *
     * @param int|Permission $permission
     * @return int
     */
    public function detachPermission($permission);

    /**
     * Detach all permissions from a user.
     *
     * @return int
     */
    public function detachAllPermissions();

    /**
     * Sync permissions for a user.
     *
     * @param array|Permission[]|Collection $permissions
     * @return array
     */
    public function syncPermissions($permissions);
}
