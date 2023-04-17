<?php

namespace App\Http\Middleware;

use Closure;

class BannedIp
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
        // Pre-Middleware Action
        $ip = $request->ip();
        $banned = \App\Models\BannedIp::where('ip_address', $ip)->count();
        if ($banned > 0) {
            return \response()->json([
                'status'  => 'error',
                'message' => 'Your IP address: '.$ip.' has been banned from accessing our servers, if you think this is a mistake. Contact techsupport@vesicash.com',
                'data'    => [
                    'ip'  => $ip,
                    'banned' => true
                ]
            ]);
        }

        return $next($request);
    }
}
