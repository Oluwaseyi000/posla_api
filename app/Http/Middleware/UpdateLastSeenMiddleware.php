<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;

class UpdateLastSeenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if(auth()->check() && auth()->user()->last_seen < now()->subMinute(15)){
            dd(auth()->user()->last_seen);
            User::where('id', auth()->user()->id)->update(['last_seen' => now()]);
        }

        return $response;

    }
}
