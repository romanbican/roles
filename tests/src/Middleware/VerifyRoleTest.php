<?php

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Ultraware\Roles\Exceptions\RoleDeniedException;
use Ultraware\Roles\Middleware\VerifyRole;

class VerifyRoleTest extends TestCase
{
    public function testUserHasPermission()
    {
        $guard = \Mockery::mock(Guard::class);
        $user = \Mockery::mock(User::class);
        $request = Request();
        $guard->shouldReceive('check')->once()->withNoArgs()->andReturn(true);
        $guard->shouldReceive('user')->once()->withNoArgs()->andReturn($user);
        $user->shouldReceive('hasRole')->once()->with('role1')->andReturn(true);

        $verifyRole = new VerifyRole($guard);
        $result = $verifyRole->handle($request, function (Request $request) {
            return 'next was called';
        }, 'role1');
        $this->assertEquals('next was called', $result);
    }

    public function testUserHasPermission_throwsException()
    {
        $guard = \Mockery::mock(Guard::class);
        $user = \Mockery::mock(User::class);
        $request = new Request();
        $guard->shouldReceive('check')->once()->withNoArgs()->andReturn(true);
        $guard->shouldReceive('user')->once()->withNoArgs()->andReturn($user);
        $user->shouldReceive('hasRole')->once()->with('role1')->andReturn(false);

        $this->expectException(RoleDeniedException::class);
        $verifyRole = new VerifyRole($guard);
        $verifyRole->handle($request, function (Request $request) {
        }, 'role1');
    }
}
