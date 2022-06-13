<?php

namespace App\Http\Middleware\Admin;

use Closure;
use App\Models\Admin;
use App\Helpers\AppConstant;
use Illuminate\Http\Request;

class IsAdminMiddleware
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
        if ($request->user() instanceof Admin && auth()->user()->tokenCan('role:'.AppConstant::ROLE_ADMIN)) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response([
                'status' => false,
                'message' => 'unauthorize'
            ], 401);
        } else {
            return redirect(route('adminLogin'));
        }


    }
}
