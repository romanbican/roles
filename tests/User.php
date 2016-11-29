<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Ultraware\Roles\Traits\HasRoleAndPermission;

class User extends Model
{
    use HasRoleAndPermission;
}
