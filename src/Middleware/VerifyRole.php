<?php

namespace Ultraware\Roles\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Ultraware\Roles\Exceptions\RoleDeniedException;

class VerifyRole
{
    /**
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @param int|string $role
     * @return mixed
     * @throws RoleDeniedException
     */
    public function handle($request, Closure $next, $role)
    {
        if ($this->auth->check() && $this->auth->user()->hasRole($role)) {
            return $next($request);
        }

        throw new RoleDeniedException($role);
    }
}
