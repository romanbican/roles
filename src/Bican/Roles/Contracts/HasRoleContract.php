<?php namespace Bican\Roles\Contracts;

interface HasRoleContract {

    /**
     * User belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles();

    /**
     * Check if the user has a provided role or roles.
     *
     * @param int|string|array $role
     * @param string $methodName
     * @return bool
     * @throws InvalidArgumentException
     */
    public function is($role, $methodName = 'One');

    /**
     * Attach role.
     *
     * @param int|Role $role
     * @return mixed
     */
    public function attachRole($role);

    /**
     * Detach role.
     *
     * @param int|Role $role
     * @return mixed
     */
    public function detachRole($role);

    /**
     * Get users level.
     *
     * @return int
     * @throws RoleNotFoundException
     */
    public function level();

}