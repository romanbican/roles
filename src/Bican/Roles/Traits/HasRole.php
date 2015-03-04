<?php namespace Bican\Roles\Traits;

use Illuminate\Database\Eloquent\Collection;
use Bican\Roles\Exceptions\RoleNotFoundException;
use Bican\Roles\Exceptions\InvalidArgumentException;
use Illuminate\Support\Facades\Config;

trait HasRole {
use HasPermission;
    /**
     * User belongs to many roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('Bican\Roles\Models\Role')->withTimestamps();
    }

    /**
     * Check if the user has a provided role or roles.
     *
     * @param int|string|array $role
     * @param string $methodName
     * @return bool
     * @throws \Bican\Roles\Exceptions\InvalidArgumentException
     */
    public function is($role, $methodName = 'One')
    {
        $pretend = $this->pretend('is');
        if ($pretend !== null) return $pretend;

        $this->checkMethodNameArgument($methodName);

        $roles = $this->getArrayFrom($role);

        if ($this->{'is' . ucwords($methodName)}($roles, $this->roles()->get()))
        {
            return true;
        }

        return false;
    }

    /**
     * Check if the user has at least one of provided roles.
     *
     * @param array $roles
     * @param \Illuminate\Database\Eloquent\Collection $userRoles
     * @return bool
     */
    protected function isOne(array $roles, Collection $userRoles)
    {
        foreach ($roles as $role)
        {
            if ($this->hasRole($role, $userRoles))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the user has all provided roles.
     *
     * @param array $roles
     * @param \Illuminate\Database\Eloquent\Collection $userRoles
     * @return bool
     */
    protected function isAll(array $roles, Collection $userRoles)
    {
        foreach ($roles as $role)
        {
            if ( ! $this->hasRole($role, $userRoles))
            {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if the user has provided role.
     *
     * @param int|string $providedRole
     * @param \Illuminate\Database\Eloquent\Collection $userRoles
     * @return bool
     */
    protected function hasRole($providedRole, Collection $userRoles)
    {
        $pretend = $this->pretend('hasRole');
        if ($pretend !== null) return $pretend;

        foreach ($userRoles as $role)
        {
            if ($role->id == $providedRole || $role->slug === $providedRole)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Attach role.
     *
     * @param int|\Bican\Roles\Models\Role $role
     * @return mixed
     */
    public function attachRole($role)
    {
        if ( ! $this->roles()->get()->contains($role))
        {
            return $this->roles()->attach($role);
        }

        return true;
    }

    /**
     * Detach role.
     *
     * @param int|\Bican\Roles\Models\Role $role
     * @return mixed
     */
    public function detachRole($role)
    {
        return $this->roles()->detach($role);
    }

    /**
     * Detach all roles.
     *
     * @return mixed
     */
    public function detachAllRoles()
    {
        return $this->roles()->detach();
    }

    /**
     * Get users level.
     *
     * @return int
     * @throws \Bican\Roles\Exceptions\RoleNotFoundException
     */
    public function level()
    {
        if ( $role = $this->roles()->orderBy('level', 'desc')->first())
        {
            return $role->level;
        }

        throw new RoleNotFoundException('This user has no role.');
    }

    /**
     * Allows to pretend/simulate package behavior.
     *
     * @return null|boolean
     */
    private function pretend($option = null)
    {
        $enabled = filter_var(Config::get('roles.pretend')['enabled'], FILTER_VALIDATE_BOOLEAN);

        if (!is_null($option) && $enabled && in_array($option, ['hasPermission', 'hasRole', 'allowed', 'is'])) {
            return filter_var(Config::get('roles.pretend')['options'][$option], FILTER_VALIDATE_BOOLEAN);
        }
        else
            return null;
    }

    /**
     * Get an array from provided parameter.
     *
     * @param int|string|array $value
     * @return array
     */
    private function getArrayFrom($value)
    {
        if ( ! is_array($value))
        {
            return preg_split('/ ?[,|] ?/', $value);
        }

        return $value;
    }

    /**
     * Check methodName argument.
     *
     * @param string $methodName
     * @return mixed
     * @throws \Bican\Roles\Exceptions\InvalidArgumentException
     */
    private function checkMethodNameArgument($methodName)
    {
        if (ucwords($methodName) != 'One' && ucwords($methodName) != 'All')
        {
            throw new InvalidArgumentException('You can pass only strings [one] or [all] as a second parameter in [is] or [can] method.');
        }
    }

}
