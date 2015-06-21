<?php

namespace Bican\Roles\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Bican\Roles\Exceptions\AccessDeniedException;

class VerifyPermission
{
    /**
     * @var \Illuminate\Contracts\Auth\Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param \Illuminate\Contracts\Auth\Guard $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param int|string $permission
     * @return mixed
     * @throws \Bican\Roles\Exception\AccessDeniedException
     */
    public function handle($request, Closure $next, $permission)
    {
        if ($this->auth->check() && $this->auth->user()->can($permission)) { return $next($request); }

        throw new AccessDeniedException('You don\'t have a required [' . $permission . '] permission.');
    }
}
