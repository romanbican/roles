<?php namespace Bican\Roles;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Console\AppNamespaceDetectorTrait;

class Role extends Model {

    use AppNamespaceDetectorTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'label', 'description', 'level'];

    /**
     * Role belongs to many permissions.
     *
     * @return mixed
     */
    public function permissions()
    {
        return $this->belongsToMany('Bican\Roles\Permission');
    }

    /**
     * Role belongs to many users.
     *
     * @return mixed
     */
    public function users()
    {
        return $this->belongsToMany($this->getAppNamespace() . 'User');
    }

    /**
     * Attach permission or permissions.
     *
     * @param int|array|Permission $permission
     * @return mixed
     */
    public function attachPermission($permission)
    {
        if( ! is_array($permission))
        {
            $permission = [$permission];
        }

        foreach ($permission as $perm)
        {
            if ( ! $this->permissions()->get()->contains($perm))
            {
                $this->permissions()->attach($perm);
            }
        }

        return true;
    }

    /**
     * Detach permission or permissions.
     *
     * @param int|array|Permission $permission
     * @return mixed
     */
    public function detachPermission($permission)
    {
        if( ! is_array($permission))
        {
            $permission = [$permission];
        }

        foreach ($permission as $perm)
        {
            $this->permissions()->detach($perm);
        }

        return true;
    }

}