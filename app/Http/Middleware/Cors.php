<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->isMethod('OPTIONS')){
            return response('', 200, [
                'Access-Control-Allow-Methods' => 'HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS',
                // 'Access-Control-Allow-Origin' => 'http://127.0.0.1:8000',
                'Access-Control-Allow-Headers' => 'authorization, content-type, accept, x-xsrf-token',
                'Access-Control-Allow-Credentials' => 'true'
            ]);
        }else{
           return $next($request);
        }
    }
}