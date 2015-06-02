<?php

namespace Bican\Roles\Models;

use Bican\Roles\Traits\RoleTrait;
use Bican\Roles\Traits\SlugableTrait;
use Baum\Node;
use Bican\Roles\Contracts\RoleContract;


class Role extends Node implements RoleContract
{
    use RoleTrait, SlugableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'description', 'level'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if ($connection = config('roles.connection')) { $this->connection = $connection; }
    }
}
