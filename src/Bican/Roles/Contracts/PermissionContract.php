<?php

namespace Bican\Roles\Contracts;

interface PermissionContract
{
    /**
     * Permission belongs to many roles.
     *
     * @return mixed
     */
    public function roles();

    /**
     * Permission belongs to many users.
     *
     * @return mixed
     */
    public function users();
}
