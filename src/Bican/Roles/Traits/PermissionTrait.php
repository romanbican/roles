<?php

namespace Bican\Roles\Traits;

use Illuminate\Support\Facades\Config;

trait PermissionTrait
{
    /**
     * Permission belongs to many roles.
     *
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany('Bican\Roles\Models\Role')->withTimestamps();
    }

    /**
     * Permission belongs to many users.
     *
     * @return mixed
     */
    public function users()
    {
        return $this->belongsToMany(Config::get('auth.model'))->withTimestamps();
    }
}
