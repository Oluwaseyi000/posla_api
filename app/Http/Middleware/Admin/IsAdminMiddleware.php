<?php

namespace App\Http\Middleware\Admin;

use Closure;
use App\Models\Admin;
use App\Helpers\AppConstant;
use App\Models\User;
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
        if (in_array($request->user()->role_type, [User::ROLE_TYPE_ADMIN, User::ROLE_TYPE_SUPERADMIN])) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response([
                'status' => false,
                'message' => 'unauthorize admin'
            ], 401);
        } else {
            return redirect(route('adminLogin'));
        }


    }
}
