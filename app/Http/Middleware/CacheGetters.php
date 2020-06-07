<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Redis;
use Closure;

class CacheGetters extends Middleware
{
    /**
     * cache all getters methods by route plus request body params
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $requestParams = $request->all();
        unset($requestParams['token']);
        ksort($requestParams);
        $redisKey = implode($request->segments()) . implode($requestParams);
        $redisData = Redis::get($redisKey);
        if (empty($redisData)) {
            $response = $next($request);
            if ($response->status() != 200) {
                return $response;
            }
            $redisData = $response->content();
            Redis::set($redisKey, $redisData);
        }
        return response($redisData);
    }
}
