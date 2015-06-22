<?php

namespace Bican\Roles\Exceptions;

class RoleDeniedException extends AccessDeniedException
{
    /**
     * Constructor.
     *
     * @param string $role
     */
    public function __construct($role)
    {
        $this->message = sprintf("You don't have a required ['%s'] role.", $role);
    }
}
