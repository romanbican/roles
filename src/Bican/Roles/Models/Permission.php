<?php namespace Bican\Roles\Models;

use Illuminate\Database\Eloquent\Model;
use Bican\Roles\Traits\SlugableTrait;

class Permission extends Model {

    use SlugableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'description', 'model', 'unique'];

    /**
     * Permission belongs to many roles.
     *
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany('Bican\Roles\Models\Role')->withTimestamps();
    }

}