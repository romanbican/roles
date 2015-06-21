<?php

namespace Bican\Roles\Contracts;

interface RoleHasRelations
{
    /**
     * Role belongs to many permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions();

    /**
     * Role belongs to many users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users();

    /**
     * Role belongs to parent role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent();

    public function parentRecursive();

    /**
     * Role has many children roles
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children();

    public function childrenRecursive();

    /**
     * Attach permission to a role.
     *
     * @param int|\Bican\Roles\Models\Permission $permission
     * @return int|bool
     */
    public function attachPermission($permission);

    /**
     * Detach permission from a role.
     *
     * @param int|\Bican\Roles\Models\Permission $permission
     * @return int
     */
    public function detachPermission($permission);

    /**
     * Detach all permissions.
     *
     * @return int
     */
    public function detachAllPermissions();
}
