<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Redis;
use Closure;

class ClearCache extends Middleware
{
    /**
     * clear cache
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Redis::flushDB();
        return $next($request);
    }
}
