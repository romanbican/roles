<?php namespace Bican\Roles\Contracts;

interface HasPermissionContract {

    /**
     * Get all role permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
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
     * @return bool
     * @throws \Bican\Roles\Exceptions\InvalidArgumentException
     */
    public function can($permission, $methodName = 'One');

    /**
     * Check if the user is allowed to manipulate with entity.
     *
     * @param string $providedPermission
     * @param object $entity
     * @param bool $owner
     * @return bool
     */
    public function allowed($providedPermission, $entity, $owner = true);

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