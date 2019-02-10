<?php

/**
 * 检测后台用户是否登录中间件
 */

namespace App\Http\Middleware;

use Closure;

class CheckAdminLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
//        dd($request);
        //检测session中是否有登录信息
        if (!$request->session()->has('admin')) {
            return redirect('/admin/login');
        }
        $admin = $request->session()->get('admin');
        if ($admin->status == '0') {
            return redirect('/admin/login');
        }
        return $next($request);
    }

}
