<?php

namespace Bican\Roles\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Bican\Roles\Exceptions\RoleDeniedException;

/**
 * Class VerifyRole
 * @package Bican\Roles\Middleware
 */
class VerifyRole
{

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param int|string $role
     * @param string $guard
     * @return mixed
     * @throws \Bican\Roles\Exceptions\RoleDeniedException
     */
    public function handle($request, Closure $next, $role, $guard = null)
    {

        if (Auth::guard($guard)->check() && Auth::guard($guard)->user()->isRole($role)) {
            return $next($request);
        }

        return response('Forbidden: Role Denied.', 403);
        
    }

}
