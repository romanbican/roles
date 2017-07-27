<?php

use App\User;
use Ultraware\Roles\Models\Permission;
use Ultraware\Roles\Models\Role;

class PermissionHasRelationsTest extends \TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->withFactories(__DIR__ . '/../../database/factories');
        $this->runMigrations();
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $this->setupDbConfig($app);
        parent::getEnvironmentSetUp($app);
    }

    public function testPermissionHasRoles()
    {
        $role = factory(Role::class)->create();
        $permission = factory(Permission::class)->create();
        $role->permissions()->attach($permission);
        $this->assertEquals($role->id, $permission->roles->first()->id);
        $this->assertEquals($role->slug, $permission->roles->first()->slug);
    }

    public function testPermissionHasUsers()
    {
        /** @var User $user */
        $user = factory(User::class)->create();
        $permission = factory(Permission::class)->create();
        $user->userPermissions()->attach($permission);
        $this->assertEquals($user->id, $permission->users->first()->id);
        $this->assertEquals($user->name, $permission->users->first()->name);
    }
}
