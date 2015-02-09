<?php namespace Bican\Roles;

use Illuminate\Database\Eloquent\Collection;
use Bican\Roles\Exceptions\RoleNotFoundException;
use Bican\Roles\Exceptions\InvalidArgumentException;

trait HasRole {

    /**
     * User belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('Bican\Roles\Role');
    }

    /**
     * Check if the user has a provided role or roles.
     *
     * @param int|string|array $role
     * @param string $methodName
     * @return bool
     * @throws InvalidArgumentException
     */
    public function is($role, $methodName = 'One')
    {
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
     * @param Collection $userRoles
     * @return bool
     */
    private function isOne(array $roles, Collection $userRoles)
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
     * @param Collection $userRoles
     * @return bool
     */
    private function isAll(array $roles, Collection $userRoles)
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
     * @param Collection $userRoles
     * @return bool
     */
    private function hasRole($providedRole, Collection $userRoles)
    {
        foreach ($userRoles as $role)
        {
            if ($role->id == $providedRole || $role->label === $providedRole)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Attach role.
     *
     * @param int|Role $role
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
     * @param int|Role $role
     * @return mixed
     */
    public function detachRole($role)
    {
        return $this->roles()->detach($role);
    }

    /**
     * Get users level.
     *
     * @return int
     * @throws RoleNotFoundException
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
     * @throws InvalidArgumentException
     */
    private function checkMethodNameArgument($methodName)
    {
        if (ucwords($methodName) != 'One' && ucwords($methodName) != 'All')
        {
            throw new InvalidArgumentException('You can pass only strings [one] or [all] as a second parameter in [is] or [can] method.');
        }
    }

    /**
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if (starts_with($method, 'is'))
        {
            if ($this->is(snake_case(substr($method, 2))))
            {
                return true;
            }

            return false;
        }
        elseif (starts_with($method, 'can'))
        {
            if ($this->can(snake_case(substr($method, 3))))
            {
                return true;
            }

            return false;
        }
        elseif (starts_with($method, 'allowed'))
        {
            if ($this->allowed(snake_case(substr($method, 7)), $parameters[0], (isset($parameters[1])) ? $parameters[1] : true))
            {
                return true;
            }

            return false;
        }

        return parent::__call($method, $parameters);
    }

}