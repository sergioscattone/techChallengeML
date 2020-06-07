<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;

class Authenticate extends Middleware
{
    /**
     * Refuse connection if key is not provided
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $paramToken = $request->headers->get('token');
        if (empty($paramToken)) {
            $paramToken = $request->get('token');
        }
        $token = str_replace("base64:/", "", env("APP_KEY"));
        if ($paramToken!=$token) {
            return response()->json(['error' => "Unauthorized"], 401);
        }
        return $next($request);
    }
}
