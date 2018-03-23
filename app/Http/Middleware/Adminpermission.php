<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Permission;
use App\Models\Adminusers;
use Route, URL, Auth;
class Adminpermission
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
        $previousUrl = strtolower(URL::previous());
        $routeName = starts_with(Route::currentRouteName(), 'admin.') ? Route::currentRouteName() : 'admin.' . Route::currentRouteName();
        if (!\Gate::forUser(auth('adminusers')->user())->check($routeName)) {
            if ($request->ajax() && ($request->getMethod() != 'GET')) {
                return response()->json([
                    'status' => -1,
                    'code'   => 403,
                    'msg'    => '您没有权限执行此操作',
                ]);
            } else {
                return response()->view('admin.errors.403', compact('previousUrl'));
            }
        }

        return $next($request);
    }
}
