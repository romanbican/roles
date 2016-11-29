<?php

class HasRoleAndPermissionTest extends \TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testHasPermission()
    {
        $user = \Mockery::mock(\User::class . '[hasOnePermission]');
        $user->shouldReceive('hasOnePermission')
            ->with('permission1')
            ->once()
            ->andReturn(true);
        $this->assertTrue($user->hasPermission('permission1'));
    }

    public function testHasPermission_all()
    {
        $user = \Mockery::mock(\User::class . '[hasAllPermissions]');
        $user->shouldReceive('hasAllPermissions')
            ->with(['permission1', 'permission2'])
            ->once()
            ->andReturn(true);
        $this->assertTrue($user->hasPermission(['permission1', 'permission2'], true));
    }

    public function testHasOnePermission_true()
    {
        $user = \Mockery::mock(\User::class . '[checkPermission]');
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
        $user = \Mockery::mock(\User::class . '[checkPermission]');
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
        $user = \Mockery::mock(\User::class . '[checkPermission]');
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
        $user = \Mockery::mock(\User::class . '[checkPermission]');
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
        $user = \Mockery::mock(\User::class . '[checkPermission]');
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
        $user = \Mockery::mock(\User::class . '[checkPermission]');
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

    public function testMagicCall()
    {
        $user = \Mockery::mock(\User::class . '[hasRole,hasPermission]');

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
