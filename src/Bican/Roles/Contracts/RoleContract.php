<?php

namespace Bican\Roles\Contracts;

interface RoleContract
{
    /**
     * Role belongs to many permissions.
     *
     * @return mixed
     */
    public function permissions();

    /**
     * Role belongs to many users.
     *
     * @return mixed
     */
    public function users();

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

    /**
     * Detach all permissions.
     *
     * @return int
     */
    public function detachAllPermissions();
}
