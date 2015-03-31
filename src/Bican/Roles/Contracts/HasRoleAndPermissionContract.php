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
     * Check if the user has a provided role or roles.
     *
     * @param int|string|array $role
     * @param string $methodName
     * @return bool
     * @throws \Bican\Roles\Exceptions\InvalidArgumentException
     */
    public function is($role, $methodName = 'One');

    /**
     * Attach role.
     *
     * @param int|\Bican\Roles\Models\Role $role
     * @return mixed
     */
    public function attachRole($role);

    /**
     * Detach role.
     *
     * @param int|\Bican\Roles\Models\Role $role
     * @return mixed
     */
    public function detachRole($role);

    /**
     * Detach all roles.
     *
     * @return mixed
     */
    public function detachAllRoles();

    /**
     * Get users level.
     *
     * @return int
     * @throws \Bican\Roles\Exceptions\RoleNotFoundException
     */
    public function level();

    /**
     * Get all role permissions.
     *
     * @return
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
     * Merge role permissions and user permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function permissions();

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
     * Attach permission.
     *
     * @param int|\Bican\Roles\Models\Permission $permission
     * @return mixed
     */
    public function attachPermission($permission);

    /**
     * Detach permission.
     *
     * @param int|\Bican\Roles\Models\Permission $permission
     * @return mixed
     */
    public function detachPermission($permission);

    /**
     * Detach all permissions.
     *
     * @return int
     */
    public function detachAllPermissions();
}
