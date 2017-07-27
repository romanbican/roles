<?php

namespace Ultraware\Roles\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait PermissionHasRelations
{
    /**
     * Permission belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(config('roles.models.role'))->withTimestamps();
    }

    /**
     * Permission belongs to many users.
     *
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('auth.providers.users.model'))->withTimestamps();
    }
}
