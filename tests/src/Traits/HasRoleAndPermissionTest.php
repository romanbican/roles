<?php

use App\User;
use Illuminate\Database\Eloquent\Collection;
use Ultraware\Roles\Models\Permission;
use Ultraware\Roles\Models\Role;

class HasRoleAndPermissionTest extends \TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->withFactories(__DIR__ . '/../../database/factories');
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

    public function testRolePermissions()
    {
        $this->runMigrations();

        /** @var User $user */
        $user = factory(User::class)->make();

        // roles
        $roles = new Collection([
            factory(Role::class)->create(),
            factory(Role::class)->create(),
            factory(Role::class)->create(['level' => 2]),
            factory(Role::class)->create(['level' => 3]),
        ]);
        // permissions
        /** @var Collection $permissions */
        $permissions = factory(Permission::class, 8)->create();

        // attach permissions to role
        $permissions->each(function ($permission, $key) use ($roles) {
            switch ($key) {
                case 0:
                case 1:
                    $roles->get(0)->attachPermission($permission);
                    break;
                case 2:
                case 3:
                    $roles->get(1)->attachPermission($permission);
                    break;
                case 4:
                case 5:
                    $roles->get(2)->attachPermission($permission);
                    break;
                case 6:
                case 7:
                    $roles->get(3)->attachPermission($permission);
                    break;
            }
        });
        // attach role 0 (without level)
        $user->roles()->attach($roles->get(0));

        // only permissions of role 0 should be found
        $this->assertEquals(
            $permissions->toBase()->only([0, 1])->pluck('id')->toArray(),
            $user->rolePermissions()->get()->pluck('id')->toArray());

        // reset cache
        $user->detachRole(null);

        // attach role 2 wich has a level and all lower role.permissions with lower level should also be found
        $user->roles()->attach($roles->get(2));

        $this->assertEquals(
            $permissions->toBase()->only([0, 1, 2, 3, 4, 5])->pluck('id')->toArray(),
            $user->rolePermissions()->get()->pluck('id')->toArray()
        );
    }

    public function testHasRole()
    {
        $user = \Mockery::mock(User::class . '[hasOneRole]');
        $user->shouldReceive('hasOneRole')
            ->with('role1')
            ->once()
            ->andReturn(true);
        $this->assertTrue($user->hasRole('role1'));
    }

    public function testHasRole_all()
    {
        $user = \Mockery::mock(User::class . '[hasAllRoles]');
        $user->shouldReceive('hasAllRoles')
            ->with(['role1', 'role2'])
            ->once()
            ->andReturn(true);
        $this->assertTrue($user->hasRole(['role1', 'role2'], true));
    }

    public function testHasOneRole_true()
    {
        $user = \Mockery::mock(User::class . '[checkRole]');
        $user->shouldReceive('checkRole')
            ->once()
            ->with('role1')
            ->andReturn(false);

        $user->shouldReceive('checkRole')
            ->once()
            ->with('role2')
            ->andReturn(true);

        $this->assertTrue($user->hasOneRole(['role1', 'role2']));
    }

    public function testHasOneRole_false()
    {
        $user = \Mockery::mock(User::class . '[checkRole]');
        $user->shouldReceive('checkRole')
            ->once()
            ->with('role1')
            ->andReturn(false);

        $user->shouldReceive('checkRole')
            ->once()
            ->with('role2')
            ->andReturn(false);

        $this->assertFalse($user->hasOneRole(['role1', 'role2']));
    }

    public function testHasAllRoles_true()
    {
        $user = \Mockery::mock(User::class . '[checkRole]');
        $user->shouldReceive('checkRole')
            ->once()
            ->with('role1')
            ->andReturn(true);

        $user->shouldReceive('checkRole')
            ->once()
            ->with('role2')
            ->andReturn(true);

        $this->assertTrue($user->hasAllRoles(['role1', 'role2']));
    }

    public function testHasOAllRole_false()
    {
        $user = \Mockery::mock(User::class . '[checkRole]');
        $user->shouldReceive('checkRole')
            ->once()
            ->with('role1')
            ->andReturn(true);

        $user->shouldReceive('checkRole')
            ->once()
            ->with('role2')
            ->andReturn(false);

        $this->assertFalse($user->hasAllRoles(['role1', 'role2']));
    }

    public function testHasAllRoles_csv()
    {
        $user = \Mockery::mock(User::class . '[checkRole]');
        $user->shouldReceive('checkRole')
            ->once()
            ->with('role1')
            ->andReturn(true);

        $user->shouldReceive('checkRole')
            ->once()
            ->with('role2')
            ->andReturn(true);

        $this->assertTrue($user->hasAllRoles('role1,role2'));
    }

    public function testHasAllRoles_pipe()
    {
        $user = \Mockery::mock(User::class . '[checkRole]');
        $user->shouldReceive('checkRole')
            ->once()
            ->with('role1')
            ->andReturn(true);

        $user->shouldReceive('checkRole')
            ->once()
            ->with('role2')
            ->andReturn(true);

        $this->assertTrue($user->hasAllRoles('role1| role2'));
    }

    public function testCheckRole()
    {
        $user = \Mockery::mock(User::class . '[getRoles]');
        $roles = factory(Role::class, 4)->make();
        $user->shouldReceive('getRoles')
            ->once()
            ->withNoArgs()
            ->andReturn($roles);

        $this->assertTrue($user->checkRole($roles->first()->id));
    }

    public function testHasPermission()
    {
        $user = \Mockery::mock(User::class . '[hasOnePermission]');
        $user->shouldReceive('hasOnePermission')
            ->with('permission1')
            ->once()
            ->andReturn(true);
        $this->assertTrue($user->hasPermission('permission1'));
    }

    public function testHasPermission_all()
    {
        $user = \Mockery::mock(User::class . '[hasAllPermissions]');
        $user->shouldReceive('hasAllPermissions')
            ->with(['permission1', 'permission2'])
            ->once()
            ->andReturn(true);
        $this->assertTrue($user->hasPermission(['permission1', 'permission2'], true));
    }

    public function testHasOnePermission_true()
    {
        $user = \Mockery::mock(User::class . '[checkPermission]');
        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission1')
            ->andReturn(false);

        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission2')
            ->andReturn(true);

        $this->assertTrue($user->hasOnePermission(['permission1', 'permission2']));
    }

    public function testHasOnePermission_false()
    {
        $user = \Mockery::mock(User::class . '[checkPermission]');
        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission1')
            ->andReturn(false);

        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission2')
            ->andReturn(false);

        $this->assertFalse($user->hasOnePermission(['permission1', 'permission2']));
    }

    public function testHasAllPermissions_true()
    {
        $user = \Mockery::mock(User::class . '[checkPermission]');
        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission1')
            ->andReturn(true);

        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission2')
            ->andReturn(true);

        $this->assertTrue($user->hasAllPermissions(['permission1', 'permission2']));
    }

    public function testHasOAllPermission_false()
    {
        $user = \Mockery::mock(User::class . '[checkPermission]');
        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission1')
            ->andReturn(true);

        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission2')
            ->andReturn(false);

        $this->assertFalse($user->hasAllPermissions(['permission1', 'permission2']));
    }

    public function testHasAllPermissions_csv()
    {
        $user = \Mockery::mock(User::class . '[checkPermission]');
        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission1')
            ->andReturn(true);

        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission2')
            ->andReturn(true);

        $this->assertTrue($user->hasAllPermissions('permission1,permission2'));
    }

    public function testHasAllPermissions_pipe()
    {
        $user = \Mockery::mock(User::class . '[checkPermission]');
        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission1')
            ->andReturn(true);

        $user->shouldReceive('checkPermission')
            ->once()
            ->with('permission2')
            ->andReturn(true);

        $this->assertTrue($user->hasAllPermissions('permission1| permission2'));
    }

    public function testCheckPermission()
    {
        $user = \Mockery::mock(User::class . '[getPermissions]');
        $permissions = factory(Permission::class, 4)->make();
        $user->shouldReceive('getPermissions')
            ->once()
            ->withNoArgs()
            ->andReturn($permissions);

        $this->assertTrue($user->checkPermission($permissions->first()->id));
    }

    public function testMagicCall()
    {
        $user = \Mockery::mock(User::class . '[hasRole,hasPermission]');

        //isMyRole
        $user->shouldReceive('hasRole')
            ->once()
            ->with('my.role')
            ->andReturn(true);
        $this->assertTrue($user->callMagic('isMyRole', []));

        //canMyPermission
        $user->shouldReceive('hasPermission')
            ->once()
            ->with('my.permission')
            ->andReturn(true);
        $this->assertTrue($user->callMagic('canMyPermission', []));
    }
}
