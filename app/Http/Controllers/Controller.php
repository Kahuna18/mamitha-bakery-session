<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    protected function getDashboardRoute(): string
    {
        return Auth::user() && Auth::user()->isAdmin()
            ? route('admin.dashboard', absolute: false)
            : (Auth::user() && Auth::user()->isKitchen()
                ? route('kitchen.dashboard', absolute: false)
                : route('order.create', absolute: false));
    }
}
