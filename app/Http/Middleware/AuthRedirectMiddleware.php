<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AdminController;
use Closure;
use Illuminate\Support\Facades\Session;

class AuthRedirectMiddleware
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
        if (
            in_array(url()->current(), [url('/'), route('register'), route('login')])
            && (auth()->check() || $request->session()->get('admin'))
        ) {
            if ($request->session()->get('admin') === 1) {
                return redirect(AdminController::DASHBOARD);
            }

            return redirect('/' . $request->session()->get('locale', 'en') . '/' . auth()->user()->id);
        }

        return $next($request);
    }
}