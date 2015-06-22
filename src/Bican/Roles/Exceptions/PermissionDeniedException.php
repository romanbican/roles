<?php

namespace Bican\Roles\Exceptions;

class PermissionDeniedException extends AccessDeniedException
{
    /**
     * Constructor.
     *
     * @param string $permission
     */
    public function __construct($permission)
    {
        $this->message = sprintf("You don't have a required ['%s'] permission.", $permission);
    }
}
