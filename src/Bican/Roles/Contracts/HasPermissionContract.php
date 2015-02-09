<?php namespace Bican\Roles\Contracts;

interface HasPermissionContract {

    /**
     * Get all role permissions.
     *
     * @return Collection
     * @throws RoleNotFoundException
     */
    public function rolePermissions();

    /**
     * User belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function userPermissions();

    /**
     * Merge role permissions and user permissions.
     *
     * @return Collection
     */
    public function permissions();

    /**
     * Check if the user has a permission or permissions.
     *
     * @param int|string|array $permission
     * @param string $methodName
     * @return bool
     * @throws InvalidArgumentException
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
     * @param int|Permission $permission
     * @return mixed
     */
    public function attachPermission($permission);

    /**
     * Detach permission.
     *
     * @param int|Permission $permission
     * @return mixed
     */
    public function detachPermission($permission);

}