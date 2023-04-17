<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\AccessTokens;

class Api
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

        $public_key  = $request->header('v-public-key');
        $private_key = $request->header('v-private-key');
    
        $auth = AccessTokens::where('private_key', $private_key)->orWhere('public_key', $public_key)->where('is_live', true)->count();


        if ($auth == 0) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
