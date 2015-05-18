<?php

namespace Bican\Roles\Contracts;

interface HasRoleAndPermissionContract
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
     * @param string $methodName
     * @return bool
     * @throws \Bican\Roles\Exceptions\InvalidArgumentException
     */
    public function is($role, $methodName = 'One');

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
     * @throws \Bican\Roles\Exceptions\RoleNotFoundException
     */
    public function level();

    /**
     * Get all permissions from roles.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws \Bican\Roles\Exceptions\RoleNotFoundException
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
     * @param string $methodName
     * @param string $from
     * @return bool
     * @throws \Bican\Roles\Exceptions\InvalidArgumentException
     */
    public function can($permission, $methodName = 'One', $from = '');

    /**
     * Check if the user is allowed to manipulate with entity.
     *
     * @param string $providedPermission
     * @param object $entity
     * @param bool $owner
     * @param string $ownerColumn
     * @return bool
     */
    public function allowed($providedPermission, $entity, $owner = true, $ownerColumn = 'user_id');

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
