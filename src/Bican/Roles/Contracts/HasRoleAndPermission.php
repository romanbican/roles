<?php

namespace Bican\Roles\Contracts;

use Illuminate\Database\Eloquent\Model;

interface HasRoleAndPermission
{
    /**
     * User belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles();

    /**
     * Get all roles as collection.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRoles();

    /**
     * Check if the user has a role or roles.
     *
     * @param int|string|array $role
     * @param bool $all
     * @return bool
     */
    public function is($role, $all = false);

    /**
     * Check if the user has all roles.
     *
     * @param int|string|array $role
     * @return bool
     */
    public function isAll($role);

    /**
     * Check if the user has at least one role.
     *
     * @param int|string|array $role
     * @return bool
     */
    public function isOne($role);

    /**
     * Check if the user has role.
     *
     * @param int|string $role
     * @return bool
     */
    public function hasRole($role);

    /**
     * Attach role to a user.
     *
     * @param int|\Bican\Roles\Models\Role $role
     * @return null|bool
     */
    public function attachRole($role);

    /**
     * Detach role from a user.
     *
     * @param int|\Bican\Roles\Models\Role $role
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
     * Get role level of a user.
     *
     * @return int
     */
    public function level();

    /**
     * Get all permissions from roles.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function rolePermissions();

    /**
     * User belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function userPermissions();

    /**
     * Get all permissions as collection.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPermissions();

    /**
     * Check if the user has a permission or permissions.
     *
     * @param int|string|array $permission
     * @param bool $all
     * @return bool
     */
    public function can($permission, $all = false);

    /**
     * Check if the user has all permissions.
     *
     * @param int|string|array $permission
     * @return bool
     */
    public function canAll($permission);

    /**
     * Check if the user has at least one permission.
     *
     * @param int|string|array $permission
     * @return bool
     */
    public function canOne($permission);

    /**
     * Check if the user has a permission.
     *
     * @param int|string $permission
     * @return bool
     */
    public function hasPermission($permission);

    /**
     * Check if the user is allowed to manipulate with entity.
     *
     * @param string $providedPermission
     * @param \Illuminate\Database\Eloquent\Model $entity
     * @param bool $owner
     * @param string $ownerColumn
     * @return bool
     */
    public function allowed($providedPermission, Model $entity, $owner = true, $ownerColumn = 'user_id');

    /**
     * Attach permission to a user.
     *
     * @param int|\Bican\Roles\Models\Permission $permission
     * @return null|bool
     */
    public function attachPermission($permission);

    /**
     * Detach permission from a user.
     *
     * @param int|\Bican\Roles\Models\Permission $permission
     * @return int
     */
    public function detachPermission($permission);

    /**
     * Detach all permissions from a user.
     *
     * @return int
     */
    public function detachAllPermissions();
}
