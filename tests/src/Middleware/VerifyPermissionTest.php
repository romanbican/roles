<?php

use App\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Ultraware\Roles\Exceptions\PermissionDeniedException;
use Ultraware\Roles\Middleware\VerifyPermission;

class VerifyPermissionTest extends TestCase
{
    public function testUserHasPermission()
    {
        $guard = \Mockery::mock(Guard::class);
        $user = \Mockery::mock(User::class);
        $request = new Illuminate\Http\Request();
        $guard->shouldReceive('check')->once()->withNoArgs()->andReturn(true);
        $guard->shouldReceive('user')->once()->withNoArgs()->andReturn($user);
        $user->shouldReceive('hasPermission')->once()->with('permission1')->andReturn(true);

        $verifyPermission = new VerifyPermission($guard);
        $result = $verifyPermission->handle($request, function (Request $request) {
            return 'next was called';
        }, 'permission1');
        $this->assertEquals('next was called', $result);
    }

    public function testUserHasPermission_throwsException()
    {
        $guard = \Mockery::mock(Guard::class);
        $user = \Mockery::mock(User::class);
        $request = new Illuminate\Http\Request();
        $guard->shouldReceive('check')->once()->withNoArgs()->andReturn(true);
        $guard->shouldReceive('user')->once()->withNoArgs()->andReturn($user);
        $user->shouldReceive('hasPermission')->once()->with('permission1')->andReturn(false);

        $this->expectException(PermissionDeniedException::class);
        $verifyPermission = new VerifyPermission($guard);
        $verifyPermission->handle($request, function (Request $request) {
        }, 'permission1');
    }
}
