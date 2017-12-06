<?php

namespace App\Http\Middleware;

use Closure;

class JsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      json_decode($request->getContent(), true);

      if (json_last_error() !== JSON_ERROR_NONE) {
        return response()->json([
          'error' => 'Invalid json request'
        ])->setStatusCode(400);
      }

      return $next($request);
    }
}