<?php namespace Bican\Roles;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['label', 'name', 'model', 'unique'];

    /**
     * Permission belongs to many roles.
     *
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany('Bican\Roles\Role');
    }

}