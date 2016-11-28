<?php

namespace Ultraware\Roles\Exceptions;

class RoleDeniedException extends AccessDeniedException
{
    /**
     * Create a new role denied exception instance.
     *
     * @param string $role
     */
    public function __construct($role)
    {
        $this->message = sprintf("You don't have a required ['%s'] role.", $role);
    }
}
