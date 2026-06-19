<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::user();
                $route = $user && $user->isAdmin()
                    ? route('admin.dashboard', absolute: false)
                    : ($user && $user->isKitchen()
                        ? route('kitchen.dashboard', absolute: false)
                        : route('order.create', absolute: false));

                return redirect($route);
            }
        }

        return $next($request);
    }
}
