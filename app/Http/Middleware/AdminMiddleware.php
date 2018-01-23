<?php

namespace App\Http\Middleware;

use Closure;
use Gate;

class AdminMiddleware
{
    public function __construct()
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Gate::denies('admin')) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
