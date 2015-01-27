<?php namespace Bican\Roles\Contracts;

interface HasRoleContract {

    /**
     * User has one role.
     *
     * @return mixed
     * @throws RoleNotFoundException
     */
    public function role();

    /**
     * User has many permissions.
     *
     * @return mixed
     */
    public function permissions();

    /**
     * Check if a user has role.
     *
     * @param int|string $role
     * @return bool
     */
    public function is($role);

    /**
     * Check if a user has provided permission or permissions.
     *
     * @param int|string|array $permissions
     * @param string $methodName
     * @return bool
     */
    public function can($permissions, $methodName = 'One');

    /**
     * Check if user has at least one of provided permissions.
     *
     * @param array $permissions
     * @return bool
     */
    public function canOne(array $permissions);

    /**
     * Check if user has all provided permissions.
     *
     * @param array $permissions
     * @return bool
     */
    public function canAll(array $permissions);

    /**
     * Check if a user has provided permission.
     *
     * @param int|string $providedPermission
     * @return bool
     */
    public function hasPermission($providedPermission);

    /**
     * Assign (change) role.
     *
     * @param int|Role $role
     * @return mixed
     */
    public function assignRole($role);

}