<?php namespace Bican\Roles\Contracts;

interface HasRoleContract {

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

}